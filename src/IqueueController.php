<?php

namespace Hasnularief\Iqueue;

use Illuminate\Http\Request;
use Hasnularief\Iqueue\Iqueue;
use Hasnularief\Iqueue\IqueueEvent;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Image;
use Auth;
use Mike42\Escpos\CapabilityProfile;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class IqueueController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function tv(Request $request) // COMPLETE
    {
      
      if(!$request->location)
        abort(404);
      
      $location = $request->location;

      Iqueue::whereDate('created_at','<>', date('Y-m-d'))->delete();

      $data = Iqueue::where('location', $location)
                     ->whereNotNull('called_at')
                     ->orderBy('called_at','desc')
                     ->select('number','counter', 'type','name')
                     ->get();  

      $data = $data->unique('counter')->groupBy('counter')->flatten(2);
       
      $counters = [];
      $counter_configurations = config('iqueue.locations.' . $location . '.counters');
      foreach($counter_configurations as $key => $val){
        $d = $data->where('counter', ($key + 1))->first();
        if(substr($val,0,1) !== '!'){
          $counters[$val] = [
            "counter" => ($key + 1), 
            "number" => $d ? $d->number : 0, 
            "type" => $d ? $d->type : '-', 
            'name' => $d ? $d->name : '-'];
        }
      }

      $tv_blade = config('iqueue.locations.' . $location . '.tv_blade' ); 

      if(view()->exists($tv_blade))
          return view($tv_blade);
      
      return view('iqueue::publish.iqueue_tv', compact('counters'));

    }

    public function counter(Request $request)
    {
      $data = [];
      $location = config('iqueue.locations.' . $request->location . '.alias');
      $types = config("iqueue.locations." . $request->location . '.types');
      
      foreach ($types as $type) {
        $data[$type] = $type;
      }
     
      $counter = array_search($request->key, config('iqueue.locations.'. $request->location . '.counters')) + 1;

      return response()->json([ "location" => $location,  "counter" => $counter,  "type" => $data ]);
    }


    public function call(Request $request)
    {
      $key = config("iqueue.locations." . $request->location . ".counters");
      
      abort_if(!$key || !$request->key, 404);

      $counter = array_search($request->key, $key);

      abort_if(!$counter, 404);
      
      $counter += 1;

      if($request->mode == 'CALL'){
        $d = Iqueue::where('location', $request->location)
                  ->where('type', $request->type)
                  ->whereDate('created_at', date('Y-m-d'))
                  ->whereNull('called_at')
                  ->orderBy('number')
                  ->first();

        if($d){
          $d->called_at = date('Y-m-d H:i:s');
          $d->counter = $counter;
          $d->save();

          broadcast(new IqueueEvent($request->location, $counter, $d));
        }          
      }
      elseif($request->mode == 'RECALL'){
        $d = Iqueue::where('location', $request->location)
                    ->where('counter',$counter)
                    ->whereNotNull('called_at')
                    ->whereDate('created_at', date('Y-m-d'))
                    ->orderBy('called_at','desc')->first();  
                    
        if($d){
          broadcast(new IqueueEvent($request->location, $counter, $d));
        }             
      }

      return $d ? $d->type."-".$d->number : "-"; 
    }

    private function last(Request $request)
    {
      abort_if(!$request->location, 404);

      $location = $request->location;

      $last = Iqueue::where('location', $location)
               ->orderBy('created_at','desc')
               ->select('number','type','created_at')
               ->get()
               ->unique('type')
               ->mapWithKeys(function($item, $key){
                  $m = collect($item);
                  return [$m['type'] => $m['number']];
               });

      return response()->json($last);
    }

    public function ticket(Request $request)
    {
    	abort_if(!$request->location, 404);              

      $location = $request->location;

      $ticket_blade = config('iqueue.locations.' . $location . '.ticket_blade' ); 

      if(view()->exists($ticket_blade))
          return view($ticket_blade);

		  return view('iqueue::publish.iqueue_ticket', compact('location'));
    }

    public function print(Request $request)
    {

    	$this->validate($request,[
				'location' => 'required',
				//'type'     => 'required',
			]);

      $location = $request->location;
      $type     = $request->type ?: '';
      $title    = str_replace('_',' ', $request->key);

    	$current = Iqueue::where('location', $location)
                      ->where('type', $type)
                      ->whereDate('created_at', date('Y-m-d'))
                      ->orderBy('number','desc')
                      ->first();


      $nextNumber = $current ? ($current->number + 1) : 1; 

      $next = new Iqueue();
      $next->location = $location;
      $next->type     = $type ?: '';
      $next->number   = $nextNumber;
      $next->save();

      $notCalled = Iqueue::where('location', $location)->where('type', $request->type)->whereNull('called_at')->count();

      $combined = $type ? ($type.'-'.$nextNumber) : $nextNumber;


      $profile = CapabilityProfile::load("simple");  

      $printer_type = config('iqueue.locations.' . $location . '.printer_type');
      $printer_string = config('iqueue.locations.' . $location . '.printer');
      $alias = config('iqueue.locations.' . $location . '.alias') ?: $location;
      
      if($printer_type == 'windows')
        $connector = new WindowsPrintConnector($printer_string);
      else
        $connector = new NetworkPrintConnector($printer, 9100);    
        
        $printer = new Printer($connector, $profile);
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $img = Image::canvas(217,89);
        $img->text($combined, 100, 30, function($font){
          $font->file(public_path('/iqueue/fonts/tahoma.ttf'));
          $font->size(80);
          $font->align('center');
          $font->valign('top');
        });
        $img->save(public_path('iqueue/ticket/' . $combined . '.jpg'));
        $text = EscposImage::load(public_path('iqueue/ticket/' . $combined . '.jpg'));        
      
      for($i = 0; $i < 1; $i++)
      {
        $printer->setTextSize(2,2);
        $printer->text('Antrian ' . ucwords($alias));
        $printer->feed(); 
         $printer->bitImageColumnFormat($text, Printer::IMG_DOUBLE_WIDTH | Printer::IMG_DOUBLE_HEIGHT);
        //$printer->graphics($text);
        $printer->feed(); 
        $printer->setTextSize(1,1);
        $printer->text('Antrian : ' . $alias);
        $printer->feed(); 
        $printer->text("Jam : " . date("Y-m-d H:i:s") . " WIB");
        $printer->feed();

        if($location == "pharmacy" && $type == 'R'){
          $printer -> text("Obat Antrian (R) Harus Diracik"); 
          $printer -> feed(); 
          $printer -> text("Lebih Dahulu"); 
          $printer -> feed(); 
          $printer -> text("Harap Sabar Menunggu"); 
          $printer -> feed(2);
        }
        else{
          $printer -> text("Silakan menunggu nomor anda dipanggil"); 
          $printer -> feed(); 
          $printer -> text("Antrian yang belum dipanggil " . $notCalled .  " orang"); 
          $printer -> feed(2);
        }
        $printer -> cut();
          
      }

      $printer -> close();

      return $this->last($request);
      }
}

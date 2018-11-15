<?php

namespace Hasnularief\Iqueue;

use Illuminate\Http\Request;
use Hasnularief\Iqueue\Iqueue;
use App\Events\TvQueue;
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
      Auth::LoginUsingId(config('iqueue.user_id'));
      
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
      $location = "";
      $obj_key = "";
      $counter = 0;
      foreach(collect(config("q.queue.counter")) as $key => $val){
      
        if(in_array($request->key, $val)){
          $location = str_replace('_',' ',$key);
          $obj_key = $key;
          $counter = array_search($request->key, $val) + 1;
          foreach(config("q.queue.locations.".$key) as $idx => $type){
            $data[$type] = config("q.queue.names.".$key)[$idx] ?? "Antrian";
          }
       }
      }

      return response()->json(["obj_key" => $obj_key, "location" => ucwords($location), "counter" => $counter, "type" => $data]);
    }


    public function call(Request $request)
    {
      $key = config("q.queue.counter.".$request->location);
      if(!$key || !$request->key)
        abort(404);

      $counter = array_search($request->key, $key);
      if($counter)
        $counter += 1;
      else
        abort(404);

      if($request->mode == 'CALL'){
        $d = Queue::where('location', $request->location)
                  ->where('type', $request->type)
                  ->whereNull('called_at')
                  ->orderBy('number')
                  ->first();

        if($d){
          $d->called_at = date('Y-m-d H:i:s');
          $d->counter = $counter;
          $d->save();

          broadcast(new TvQueue($request->location, $counter, $d));
        }          
      }
      elseif($request->mode == 'RECALL'){
        $d = Queue::where('location', $request->location)
                    ->where('counter',$counter)
                    ->whereNotNull('called_at')
                    ->orderBy('called_at','desc')->first();  
                    
        if($d){
          broadcast(new TvQueue($request->location, $counter, $d));
        }             
      }

      return $d ? $d->type."-".$d->number : "-"; 
    }

    public function last(Request $request)
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
      Auth::LoginUsingId(config('iqueue.user_id'));

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

      return $this->last($request);

      for($i = 0; $i < 1; $i++)
      {
        //$connector = new NetworkPrintConnector(config("q.queue.printer.".$location), 9100);    
        $profile = CapabilityProfile::load("simple");  
        // $profile = CapabilityProfile::load("SP2000");  
        
        $connector = new WindowsPrintConnector("smb://192.168.10.200/Epson TM-U220 Receipt");
        $printer = new Printer($connector, $profile);
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $img = Image::canvas(217,89);
        $img->text($combined, 100, 30, function($font){
          // $font->file(public_path('/assets/fonts/tahoma.ttf'));
          $font->size(80);
          $font->align('center');
          $font->valign('top');
        });
        $img->save(public_path('ticket/' . $combined . '.jpg'));
        $text = EscposImage::load(public_path('ticket/' . $combined . '.jpg'));        
        $printer->setTextSize(2,2);
        $printer->text('Antrian ' . ucwords($title));
        $printer->feed(); 
         $printer->bitImageColumnFormat($text, Printer::IMG_DOUBLE_WIDTH | Printer::IMG_DOUBLE_HEIGHT);
        //$printer->graphics($text);
        $printer->feed(); 
        $printer->setTextSize(1,1);
        $printer->text('Antrian : ' . $name);
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
        $printer -> close();           
      }

      return $this->last($request);
      }
}

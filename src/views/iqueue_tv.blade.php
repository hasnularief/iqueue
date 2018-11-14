<?php $title = str_replace('_',' ',request()->location) ?>
<html>
  <head>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Scripts -->
    <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.slim.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vue.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/axios.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/socket.io.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/echo.js') }}"></script>
    <script type="text/javascript">
      const _HOST     = "{{url('')}}";
      const _LOCATION = "{{request()->location}}";
      const _TYPES    = <?php echo json_encode(config('q.queue.locations.'.request()->location)) ?>;
      const _NAMES    = <?php echo json_encode(config('q.queue.names.'.request()->location)) ?>;
      const _TITLE    = "{{ucfirst($title)}}";
    </script>
    <style type="text/css">
      .tv-queue .flex-item{
        min-height: 125px !important;
        text-align: center;
      }
      .tv-queue .card-body{
        padding: 5px !important;
      }
      .tv-queue .tv-box{
        margin-top: 10px !important;
        margin-bottom: 10px !important;
      }

      .tv-queue .card-header{
        text-align: center;
        background-color: #333 !important;
        color: white !important;
      }

      .tv-box .card{
        border: 2px solid #333 !important;
      }

      .tv-queue .queue-number{
        width: 100%;
        text-align: center;
        font-size: 40pt;
        font-weight: 650;
      }
      .tv-queue .queue-name{
        width: 100%;
        text-align: center;
        font-size: 20pt;
      }

    </style>
  </head>
  <body>
  	@verbatim
  	<main id="vue-app" class="pt-4">
      <audio id="player"></audio>
      <h1 class="text-center location">INSTALASI FARMASI USU</h1>
  		<div class="container tv-queue">
        <div class="row">
          <div class="col-lg-8">
             <div class="card">
              <div class="card-body">
                <div class="queue-media">
                  
                </div>
              </div>
             </div>
          </div>
          <div class="col-lg-4">
            <div class="card">
              <div class="card-body row justify-content-center">
                <div v-for="(m, index, key) in models" class="col-lg-12 tv-box">
                  <div v-if="true" class="card">
                    <div class="card-header"><h3>Counter : {{m.counter}}</h3></div>
                    <div class="card-body">
                      <div class="flex-container flex-one">
                        <div class="flex-item">
                          <div>
                            <span v-if="m.number && m.type" class="queue-number">{{m.type + " - " +m.number}}</span>
                            <span v-else class="queue-number">---</span>
                            <br>
                            <span class="queue-name">{{m.name}}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- <div class="col-lg-12">
            <div class="card">
              <div class="card-body row justify-content-center">
                <div v-for="(m, index, key) in models" class="col-lg-4 tv-box">
                  <div v-if="key!==''" class="card">
                    <div class="card-header"><h3>Counter : {{m.counter}}</h3></div>
                    <div class="card-body">
                      <div class="flex-container flex-one">
                        <div class="flex-item">
                          <div>
                            <span v-if="m.number && m.type" class="queue-number">{{m.type + " - " +m.number}}</span>
                            <span v-else class="queue-number">A-25</span>
                            <br>
                            <span class="queue-name">{{m.name}}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div> -->
        </div>
      </div>
  	</main>

    <script>
      window.Echo = new Echo({
        broadcaster: 'socket.io',
        host: window.location.hostname + ':6001'
      });

      player.addEventListener('ended', function(e){
          this.playMe()
      }); 

      var app = new Vue({
        el: '#vue-app',
        data: {
          host     : _HOST,
          location : _LOCATION,
          title    : _TITLE,
          types    : _TYPES,
          queue    : _NAMES,
          models: [
              @endverbatim
              @foreach($counter as $c)
                  { counter: {{$c['counter']}}, number: {{$c['number']}}, type: "{{$c['type']}}", name: "{{$c['name']}}" },
              @endforeach
              @verbatim
          ],
          playlist: [],
          nextPlaylist : [],
          playing: false,
        },

        created: function() {
          const vm = this;

          window.Echo.private('tv-queue-'+this.location)
            .listen('TvQueue', (e) => {
              console.log(e);
              vm.pushNumber(e.data.number, e.data.type);
              vm.pushCounter(e.data.counter);
              
              vm.setNumber(e.data);

              // var select = '#panel_'+e.counter+' .panel-body';

              // $(select).addClass('blink');
              // setTimeout(function(){
              //     $(select).removeClass('blink');
              // }, 2000);

              if(!vm.playing){
                  vm.newPlay();
              }     
          }) 
        },

        mounted: function(){
          const vm = this
          var player = document.getElementById("player")

          player.addEventListener('play', function(e){
            vm.playing = true
          });

          player.addEventListener('ended', function(e){
            console.log(vm.playlist)
            if(vm.playlist.length > 0)
              vm.newPlay()
            else
              vm.playing = false 
          });
        },

        methods: {
          newPlay: function(){
              const vm = this
              var i = 0; 
              var player = document.getElementById("player")

              player.src = vm.playlist[0];
              player.play();
              vm.playlist.shift();
          },
          setNumber: function(data) {
            for (var i = this.models.length - 1; i >= 0; i--) {
                if(this.models[i].counter == data.counter){
                    this.models[i].number = data.number;
                    this.models[i].type = data.type;
                    this.models[i].name = data.name;
                    return;
                }
            }
          },
          splitNumber: function(number) {
            const vm = this
            number = parseFloat(number);
            var bill = ['_','Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan','Sepuluh','Sebelas'];
            if (number < 12) {
                vm.playlist.push(vm.host + '/assets/audio/' + bill[number] + '.mp3');
            } else if (number < 20) {
                vm.playlist.push(vm.host + '/assets/audio/' + bill[number - 10] + '.mp3');
                vm.playlist.push(vm.host + '/assets/audio/Belas.mp3');
                return bill[number - 10] + ' Belas';
            } else if (number < 100) {
                var div_result = parseInt(number / 10);
                var mod_result = number % 10;
                vm.playlist.push(vm.host + '/assets/audio/' + bill[div_result] + '.mp3');
                vm.playlist.push(vm.host + '/assets/audio/Puluh.mp3');
                vm.playlist.push(vm.host + '/assets/audio/' + bill[mod_result] + '.mp3');
            } else if (number < 200) { 
                vm.playlist.push(vm.host + '/assets/audio/Seratus.mp3');
                vm.splitNumber(number - 100)
            } else if (number < 1000) { 
                var div_result = parseInt(number / 100); 
                var mod_result = number % 100; 
                vm.playlist.push(vm.host + '/assets/audio/' + bill[div_result] + '.mp3');
                vm.playlist.push(vm.host + '/assets/audio/Ratus.mp3');
                vm.splitNumber(mod_result)
            } else if (number < 2000) { 
                vm.playlist.push(vm.host + '/assets/audio/Seribu.mp3');
                vm.splitNumber(number - 1000)
            } else if (number < 1000000) { 
                var div_result = parseInt(number / 1000); 
                var mod_result = number % 1000; 
                vm.splitNumber(div_result)
                vm.playlist.push(vm.host + '/assets/audio/Ribu.mp3');
                vm.splitNumber(mod_result)
            }
          },

          pushNumber: function(number, letter){
            this.playlist.push(this.host + '/assets/audio/NoAntrian.mp3');
            this.playlist.push(this.host + '/assets/audio/'+ letter +'.mp3');
            this.splitNumber(number);
          },

          pushCounter: function(number){
            this.playlist.push(this.host + '/assets/audio/KeCounter.mp3');
            this.splitNumber(number);
          },
        }
      });
      
    </script>
    @endverbatim
  </body>
 </html>
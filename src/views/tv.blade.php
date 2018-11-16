<?php
  $location = request()->location; 
  $alias = config('iqueue.locations.' . $location . '.alias'); 
?>
<html>
  <head>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link href="{{ asset('iqueue/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Scripts -->
    <script type="text/javascript" src="{{ asset('iqueue/js/vue.js') }}"></script>
    <script type="text/javascript" src="{{ asset('iqueue/js/axios.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('iqueue/js/socket.io.js') }}"></script>
    <script type="text/javascript" src="{{ asset('iqueue/js/echo.js') }}"></script>
    <script type="text/javascript">
      const _HOST     = "{{url('')}}";
      const _LOCATION = "{{request()->location}}";
      const _ALIAS    = "{{$alias}}";
      
    </script>

    @yield('header')


</head>
  <body>
    @yield('body')
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
          alias : _ALIAS,
          models: [
              
              @foreach($counters as $c)
                  { counter: {{$c['counter']}}, number: {{$c['number']}}, type: "{{$c['type']}}", name: "{{$c['name']}}" },
              @endforeach
              
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
                vm.playlist.push(vm.host + '/iqueue/audio/' + bill[number] + '.mp3');
            } else if (number < 20) {
                vm.playlist.push(vm.host + '/iqueue/audio/' + bill[number - 10] + '.mp3');
                vm.playlist.push(vm.host + '/iqueue/audio/Belas.mp3');
                return bill[number - 10] + ' Belas';
            } else if (number < 100) {
                var div_result = parseInt(number / 10);
                var mod_result = number % 10;
                vm.playlist.push(vm.host + '/iqueue/audio/' + bill[div_result] + '.mp3');
                vm.playlist.push(vm.host + '/iqueue/audio/Puluh.mp3');
                vm.playlist.push(vm.host + '/iqueue/audio/' + bill[mod_result] + '.mp3');
            } else if (number < 200) { 
                vm.playlist.push(vm.host + '/iqueue/audio/Seratus.mp3');
                vm.splitNumber(number - 100)
            } else if (number < 1000) { 
                var div_result = parseInt(number / 100); 
                var mod_result = number % 100; 
                vm.playlist.push(vm.host + '/iqueue/audio/' + bill[div_result] + '.mp3');
                vm.playlist.push(vm.host + '/iqueue/audio/Ratus.mp3');
                vm.splitNumber(mod_result)
            } else if (number < 2000) { 
                vm.playlist.push(vm.host + '/iqueue/audio/Seribu.mp3');
                vm.splitNumber(number - 1000)
            } else if (number < 1000000) { 
                var div_result = parseInt(number / 1000); 
                var mod_result = number % 1000; 
                vm.splitNumber(div_result)
                vm.playlist.push(vm.host + '/iqueue/audio/Ribu.mp3');
                vm.splitNumber(mod_result)
            }
          },

          pushNumber: function(number, letter){
            this.playlist.push(this.host + '/iqueue/audio/NoAntrian.mp3');
            this.playlist.push(this.host + '/iqueue/audio/'+ letter +'.mp3');
            this.splitNumber(number);
          },

          pushCounter: function(number){
            this.playlist.push(this.host + '/iqueue/audio/KeCounter.mp3');
            this.splitNumber(number);
          },
        }
      });
      
    </script>
    
  </body>
 </html>
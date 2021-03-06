<?php
  $location = request()->location; 
  $alias = config('iqueue.locations.' . $location . '.alias'); 
  $types = json_encode(config('iqueue.locations.' . $location . '.types'));
?>
<html>
	<head>
	<!-- Styles -->
    <link href="{{ asset('iqueue/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('iqueue/css/alertify.css') }}" rel="stylesheet" type="text/css">

    <!-- Scripts -->
    <script type="text/javascript" src="{{ asset('iqueue/js/vue.js') }}"></script>
    <script type="text/javascript" src="{{ asset('iqueue/js/axios.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('iqueue/js/alertify.js') }}"></script>
    <script type="text/javascript">
      const _HOST     = "{{url('')}}";
      const _LOCATION = "{{$location}}";
      const _TYPES    = {!! $types !!};
      const _ALIAS    = "{{$alias}}";
    </script>

    @yield('header')
	</head>
  <body>
    @yield('body')

    <script>

      window.alertify.logPosition("bottom right");
      window.alertify.theme("bootstrap");
      var app = new Vue({
          el: '#vue-app',
          data: {
            host     : _HOST,
            location : _LOCATION,
            alias    : _ALIAS,
            types    : _TYPES,
            last     : [''],
            _TICKET_NAMES: window._TICKET_NAMES,
          },
          mounted(){
            
          },
          methods: {
            

            requestTicket(type, name) {
              const vm = this
              const param = {location : this.location, type: type, name: name};
              axios.get(this.host + '/iqueue/print', {params: param}).then(function(response){
                Vue.set(vm.$data, 'last', response.data);
                //alertify.success("BERHASIL MENCETAK TICKET");
              }).catch(function(error){
                alertify.error(error);
                //alertify.error("Error " + error.response.status + ": " + error.response.data)
              });
            }
          }
      });
      
    </script>

  </body>
  </html>
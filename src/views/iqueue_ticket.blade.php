<?php $title = str_replace('_',' ',$location) ?>
<html>
	<head>
	<!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Scripts -->
    <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.slim.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vue.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/axios.min.js') }}"></script>
    <script type="text/javascript">
      const _HOST     = "{{url('')}}";
      const _LOCATION = "{{$location}}";
      const _TYPES    = <?php echo json_encode(config('q.queue.locations.'.$location)) ?>;
      const _NAMES    = <?php echo json_encode(config('q.queue.names.'.$location)) ?>;
      const _TITLE    = "{{ucfirst($title)}}";
    </script>
	</head>
	<body>
    @verbatim
		<main id="vue-app" class="pt-4">
		  <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h2><small>Queue Location : </small>{{ title }}</h2></div>  
                    <div class="card-body row justify-content-center">
                      <div v-for="(type, index, key) in types" class="col-md-3">
                          <div class="card">
                              <div class="card-body">
                                  <div class="flex-container flex-one">
                                      <div class="flex-item">
                                          <div @click="requestTicket(type, queue[index])" class="card card-hover">
                                              <img class="card-img-top" :src="host + '/assets/ticket.png'" alt="Card image cap">
                                              <div class="card-body">
                                                  <h5 class="card-title">Print Ticket <br>{{ type }} - 
                                                    {{ queue[index] ? queue[index] : queue[0] }}
                                                  </h5>
                                               </div>
                                               <div class="overlay-ticket">{{ type }} - 00</div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    	</div>
		</main>

    <script>
      var app = new Vue({
          el: '#vue-app',
          data: {
            host     : _HOST,
            location : _LOCATION,
            title    : _TITLE,
            types    : _TYPES,
            queue    : _NAMES,
            last     : {},
          },
          mounted(){
            // this.getLast()
          },
          methods: {
            getLast(){
              const vm = this
              const param = {location : this.location};
              axios.get(_HOST + '/queue/last', {params: param}).then(function(response){
                Vue.set(vm.$data, 'last', response.data);
              }).catch(function(error){
              });
            },

            requestTicket(type, name) {
              const vm = this
              const param = {location : this.location, type: type, name: name};
              axios.get(_HOST + '/queue/print', {params: param}).then(function(response){
                vm.getLast()
              }).catch(function(error){
              });
            }
          }
      });
      
    </script>

    @endverbatim
	</body>
</html>




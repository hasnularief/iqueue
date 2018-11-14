<html>
    <head>
        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">

        <!-- Scripts -->
        <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.slim.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
    </head>
    <body>
        <main class="pt-4">
            <div class="container">
                <div class="flex-container flex-two">
                    @foreach(config('q.queue.locations') as $key => $loc)
                    <div class="flex-item" >
                        <?php $title = str_replace('_',' ',$key) ?>
                        <div class="card">
                            <div class="card-header"><h4>Queue {{ucwords($title)}}</h4></div>
                            <div class="card-body">
                                <div class="flex-container flex-two">
                                    <div class="flex-item">
                                        <div onclick="location.href='queue/tv?location={{$key}}'" class="card card-hover">
                                            <img class="card-img-top" src="{{ asset('assets/monitor.png') }}" alt="Card image cap">
                                            <div class="card-body">
                                                <h5 class="card-title">TV {{ucwords($title)}}</h5>
                                                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                                             </div>
                                        </div>
                                    </div>
                                    <div class="flex-item">
                                        <div onclick="location.href='queue/ticket?location={{$key}}'" class="card card-hover">
                                            <img class="card-img-top" src="{{ asset('assets/printer.png') }}" alt="Card image cap">
                                            <div class="card-body">
                                                <h5 class="card-title">Ticket </h5>
                                                <p class="card-text">Queue Type : <br> 
                                                    @foreach(config('q.queue.locations.'.$key) as $index => $type)
                                                        <span>{{$type}} : 
                                                                {{isset(config('q.queue.names.'.$key)[$index]) ? 
                                                                  config('q.queue.names.'.$key)[$index] : 
                                                                  config('q.queue.names.'.$key)[0]
                                                                }}&nbsp;</span><br>
                                                    @endforeach
                                                </p>
                                             </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach  
                </div>
            </div>
        </main>
    </body>
</html>
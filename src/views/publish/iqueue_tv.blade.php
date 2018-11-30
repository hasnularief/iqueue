@extends('iqueue::tv')


@section('header')
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
@endsection
  @section('body')
    <main id="vue-app" class="pt-4">
      <audio id="player"></audio>
      <h1 class="text-center location">@{{alias}}</h1>
      <div class="container tv-queue">
        <div class="row">
          <div v-for="(m, index, key) in models" class="col-lg-4 tv-box">
                  <div class="card">
                    <div class="card-header"><h3>Counter : @{{m.counter}}</h3></div>
                    <div class="card-body">
                      <div class="flex-container flex-one">
                        <div class="flex-item">
                          <div>
                            <span v-if="!m.type" class="queue-number">@{{m.number}}</span>
                            <span v-else-if="m.number && m.type" class="queue-number">@{{m.type + " - " +m.number}}</span>
                            <span v-else class="queue-number">---</span>
                            <br>
                            <span class="queue-name">@{{m.name}}</span>
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
    @endsection

    
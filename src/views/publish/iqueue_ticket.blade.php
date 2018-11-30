@extends('iqueue::ticket')

@section('header')
<script type="text/javascript">
  const _TICKET_NAMES = ['TICKET 1', 'TICKET 2', 'TICKET 3' ];
</script>
@endsection

@section('body')

<main id="vue-app" class="pt-4">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header"><h2><small>Tiket Antrian : </small>@{{ alias }}</h2></div>  
          <div class="card-body row justify-content-center">
            <div v-for="type, index in types" class="col-md-3">
              <div class="card">
                <div class="card-body">
                  <div class="flex-container flex-one">
                    <div class="flex-item">
                      <div @click="requestTicket(type, index)" class="card card-hover" style="cursor:pointer">
                        <div class="card-body" align="center">
                          <h5 class="card-title">Cetak Tiket</h5>
                          <h1><strong> @{{ type }}</strong></h1>
                        </div>
                        <div class="overlay-ticket" align="center">Nama Tiket: @{{ _TICKET_NAMES[index] }} <br>
                        Nomor Terakhir: @{{last[type]}}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
            </div>
            <div class="col-md-3" v-if="types.length == 0">
                <div class="card">
                <div class="card-body">
                  <div class="flex-container flex-one">
                    <div class="flex-item">
                      <div @click="requestTicket(null, 0)" class="card card-hover" style="cursor:pointer">
                        <div class="card-body" align="center">
                          <h5 class="card-title">Cetak Tiket</h5>
                          <h1><strong> CETAK</strong></h1>
                        </div>
                        <div class="overlay-ticket" align="center">Nomor Terakhir: @{{last['']}}</div>
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
@endsection





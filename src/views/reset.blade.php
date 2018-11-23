<!DOCTYPE html>
<html>
<head>
	<title>IQUEUE RESET</title>
	<style type="text/css">
		body {font-family: Tahoma; font-size: 11px; } h1, h2, h3, h4, h5, h6 {padding: 2px; margin: 0px; } table {font-size: 11px; border-collapse: collapse; } table tr td {font-family: Tahoma; padding: 10px; } table tr th {font-family: Tahoma; font-weight: bold; background-color: #eee; padding: 2px; } tbody td{vertical-align: top; } tbody p{margin: 0; } pre{ display: block; word-break: break-all; word-wrap: break-word; } input, textarea, select, button { font: 11px Tahoma; }
	</style>
</head>
<body>
<h1>RESET IQUEUE</h1>
<div id="v-app">
<table border="1">
	<tr>
		<th>Location</th>
		<th>Ticket</th>
		<th>Act</th>
	</tr>
	<template v-for="(location, key) in iqueue">
	<tr>
		<td>@{{location.alias ? location.alias : key}}</td>
		<td></td>
		<td><button @click="resetLocation(key)">RESET</button></td>
	</tr>
	<tr v-for="ticket in location.types">
		<td></td>
		<td>@{{ticket}}</td>
		<td><button @click="resetLocationTicket(key, ticket)">RESET</button></td>
	</tr>
	</template>
</table>
</div>
<script type="text/javascript" src="js/vue.js"></script>
<script type="text/javascript" src="js/alertify.js"></script>
<script type="text/javascript" src="js/axios.min.js"></script>
<script>
	var app = new Vue({
		el: '#v-app',
		data: {
			host: "{{url('')}}/",
			iqueue: {!! json_encode(config('iqueue.locations')) !!}
		},
		methods: {
			resetLocation(key) {
				var vm = this
				alertify.confirm('Are you sure want to reset?', function(){
					axios.post(vm.host + 'iqueue/reset?req=reset_location&location=' + key).then(function(data){
						alertify.success('Location reset');
					}).catch(function(error){
						alertify.error(error)
					});	
				});				
			},
			resetLocationTicket(key, ticket) {
				var vm = this
				alertify.confirm('Are you sure want to reset?', function(){
					axios.post(vm.host + 'iqueue/reset?req=reset_ticket&location=' + key + '&type=' + ticket).then(function(data){
						alertify.success('Ticket reset');
					}).catch(function(error){
						alertify.error(error)
					});
				});
			}
		}
	});
</script>
</body>
</html>
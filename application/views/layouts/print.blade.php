<html>
<head>
	<title> {{ $title }} </title> 
	{{ HTML::style('css/print.css') }}
	<script type="text/javascript">
		javascript:window.print()
	</script>
	
</head>
<body>

	@yield('content')

</body>
</html>
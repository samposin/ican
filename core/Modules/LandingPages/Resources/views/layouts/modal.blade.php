<!DOCTYPE html>
<html class="editor-modal">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ \Platform\Controllers\Core\Reseller::get()->name }}</title>

  <link href="{{ asset('assets/css/styles.min.css') }}" rel="stylesheet">

  <!-- Scripts -->
  <script src="{{ url('assets/javascript?lang=' . \App::getLocale()) }}"></script>

  <script>
    var app_root = "{{ url('/') }}";

    window.Laravel = <?php echo json_encode([
      'csrfToken' => csrf_token(),
    ]); ?>
  </script>

</head>

<body>
@yield('content')

  <!-- Scripts -->
  <script src="{{ url('assets/js/scripts.min.js') }}"></script>

  <script>
    $(function() {
      onPartialLoaded();

      $('.onClickClose').on('click', function() {
        window.parent.lfCloseModal();
      });

      // Focus window and bind escape to close
      $(window).focus();

      $(document).keyup(function(e) {
        if(e.keyCode === 27) {
          window.parent.lfCloseModal();
        }
      });
    });
  </script>

@yield('script')

  <!-- Fonts -->
  <link href="//fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet">
  <link href="//fonts.googleapis.com/css?family=Source+Sans+Pro:600,400,700" rel="stylesheet">
</body>
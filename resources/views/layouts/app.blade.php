<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="data:image/x-icon;base64,AAABAAEAEBAQAAEABAAoAQAAFgAAACgAAAAQAAAAIAAAAAEABAAAAAAAgAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAA////AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAEREAAAAAEREREAAAAAABEREAAREREAAREAARERERAAEAAREREREQAAAREREREREAABEREREREQAAERERERERAAAAAAAAAAAAAAAAAAAAAAARERERERERERERERERERERERAAEREAAREREAAREQABEREQABERAAERERAAEREAAREAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA" rel="icon" type="image/x-icon">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Include FontAwesome CSS -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <script>
        // Define a global JavaScript variable to hold the app URL
        window.appUrl = "{{ config('app.url') }}";
    </script>
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" ></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/Draggable.min.js"></script>

    
  <!-- Include Bootstrap CSS and JavaScript (ensure you have Bootstrap properly set up) -->



    @isset($game)
    <script>
        // Define a JavaScript variable and assign it data from PHP
        var gameData = @json($game);
    </script>
    @endisset
  <style>
    /* Default styles for the label */


    </style>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

</head>
<body>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050">
        <div id="notification-container" class="toast-container"></div>
    </div>
    <div id="app" class="min-vh-100">
        {{-- <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav> --}}
        <!-- HTML: Create a container for notifications in the top right corner -->
      
  
        <main class="">
          
            @yield('content')
        </main>
    </div>

<!-- JavaScript code -->
<script>
    function set_events() {
        // Get all divs with class "animated-div"
        var divs = document.querySelectorAll('.animated-div');


        // Add a click event listener to each div if it doesn't already have one
        divs.forEach((div) => {
            if (!div.hasEventListener) {
                div.hasEventListener = true;
                div.addEventListener('click', runAnimation);
            }
        });
    }
    

    // Function to run the GSAP animation
    function runAnimation(e) {

        // Check if the associated checkbox is checked
        var checkbox = jQuery('input[id="card-'+e.target.value+'"]');
        if (checkbox[0] != null){
            if (checkbox[0].checked) {
            // Create a GSAP animation for the div
                gsap.to(this, { y: -10, duration: 0.3 }); // Modify the animation as needed
            }
            else{
                gsap.to(this, { y: 0, duration: 0.3 }); // Modify the animation as needed
            }
        }
      
    }

    
</script>
@isset($game)

<script>
    // Wait for the DOM to be ready
document.addEventListener("DOMContentLoaded", function () {
  // Find the form by its ID or other attributes
  var form = document.getElementById("myForm");

  if (form) {
    // Attach a submit event listener to the form
    form.addEventListener("submit", function (event) {

      // Prevent the form from submitting via traditional means
      event.preventDefault();

      // Serialize the form data into a format suitable for AJAX
      var formData = new FormData(form);
      formData.append("action",event.submitter.value);

      // Create an XMLHttpRequest object
      var xhr = new XMLHttpRequest();

      // Configure the AJAX request
      xhr.open("POST", "{{ env('APP_URL') }}/games/{{ $game->id }}/action", true); // Replace with your actual endpoint URL
      xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}"); // Add CSRF token if required

      // Define a callback function to handle the AJAX response
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          // Handle the AJAX response here
          var responseData = JSON.parse(xhr.responseText);
            console.log(responseData);
            if (responseData.status == 'success'){
                showToast(responseData.message,'success', { delay: 3000 });
            }
            else if(responseData.status == 'error'){
                showToast(responseData.message, 'danger',{ delay: 3000 });
            }

        }
      };

      // Send the AJAX request with the serialized form data
      xhr.send(formData);
    });
  }
});
function handCards() {

var cardsholders = document.querySelectorAll('.draggable-item+.hand');
cardsholders.forEach(elements => {

    var cards = elements.querySelectorAll('.card');
    var cardCount = cards.length;
    var diff = 20 / (cardCount - 1);
    var start = -10;
    cards.forEach(element => {
        element.style.transform = 'skew(' + -start + 'deg, ' + start + 'deg)';
        start = start + diff;

    });
})
}



// Create a function to make items draggable and define the action
function makeItemsDraggable() {
    var items = document.querySelectorAll('.draggable-item');
  items.forEach((item) => {
    const id = item.id;

    // Make the item draggable
    const draggable = Draggable.create(item, {
      type: 'x,y',
      edgeResistance: 0.65,
      bounds: window, // Bound within the window
      onDragEnd: function () {
        // Check if the item has been dragged more than 100 pixels on the Y-axis
        if (this.y < -150) {
            var cardId = $(this.target.children[0]).attr('for');
            var card = $('label[for="'+cardId+ '"] .card');
            $("#"+cardId).prop("checked", true);
          // Perform your action here, e.g., change the background color
          
          gsap.to(card, { backgroundColor: 'yellow', duration: 0.2 });
        } else {
            var cardId = $(this.target.children[0]).attr('for');
            var card = $('label[for="'+cardId+ '"] .card');
            $("#"+cardId).prop("checked", false);
          // Reset the background color if the item is dragged back
          gsap.to(card, { backgroundColor: 'white', duration: 0.2 });
        }
      },
    });
  });
}

// Get all draggable items by class name


// Call the function to make items draggable and define the action
function showToast(message, type, options) {
    const toastContainer = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast bg-${type} text-white`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    if (options && options.delay) {
        toast.setAttribute('data-delay', options.delay);
    }
    const toastContent = document.createElement('div');
    toastContent.className = 'toast-body';
    toastContent.textContent = message;
    toast.appendChild(toastContent);
    toastContainer.appendChild(toast);
    const toastInstance = new bootstrap.Toast(toast);
    toastInstance.show();
}

function handleCheck(data){
    console.log(data.current_turn);
    if (data.current_turn){
        jQuery('.user-checks').hide()
        jQuery('#check-'+data.current_turn).show()
    }
    
    console.log();
}

</script>
@endisset
</body>
</html>

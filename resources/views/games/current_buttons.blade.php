@if ($game->status == 'starting')
<button type="submit" name="action" value="switch">Switch</button>
<button type="submit" name="action" value="ready">Ready</button>
@elseif ($game->status == 'in_progress')

<button type="submit" name="action" value="draw_pile">Take pile</button>
<button type="submit" name="action" value="play_card">Play card</button>
<button type="submit" name="action" value="send_update">send_update</button>
@endif

<script>
    // Wait for the DOM to be ready
    document.addEventListener("DOMContentLoaded", function () {
        // Find the form and button by their IDs or other attributes
        var form = document.getElementById("myForm");
        var button = form.querySelector('button[name="action"][value="send_update"]');

        // Attach a click event listener to the button
        button.addEventListener("click", function (event) {
            // Prevent the form from submitting via traditional means
            event.preventDefault();
            
            // Serialize the form data into a format suitable for AJAX
            var formData = new FormData(form);
            
            // Add the "action" parameter to the FormData object
            formData.append("action", event.target.value);

            // Create an XMLHttpRequest object
            var xhr = new XMLHttpRequest();

            // Configure the AJAX request
            xhr.open("POST", "/shithead-game/public/games/4/action", true); // Replace with your actual endpoint URL
            xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}"); // Add CSRF token if required

            // Define a callback function to handle the AJAX response
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Handle the AJAX response here
                    console.log(xhr.responseText);
                }
            };

            // Send the AJAX request with the serialized form data
            xhr.send(formData);
        });
    });
</script>


</script>
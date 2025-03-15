<?php
    if (isset($_SESSION['popup_message'])) {
        // Use Bootstrap's alert classes. "success" and "danger" here correspond to $_SESSION['popup_type']
        $alertClass = ($_SESSION['popup_type'] === 'success') ? 'alert-success' : 'alert-danger';
        echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert" id="popupMessage">'
            . $_SESSION['popup_message'] .
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">
             <span aria-hidden="true">&times;</span>
          </button>
         </div>';
        // Remove the message after displaying it
        unset($_SESSION['popup_message']);
        unset($_SESSION['popup_type']);
    }

    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                var popup = document.getElementById('popupMessage');
                if (popup) {
                    popup.style.transition = "opacity 1s ease";
                    popup.style.opacity = 0;
                    setTimeout(function () {
                        if (popup.parentNode) {
                            popup.parentNode.removeChild(popup);
                        }
                    }, 1500);
                }
            }, 1500);
        });
    </script>
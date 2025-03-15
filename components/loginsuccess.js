function showAlert() {
    // Create the alert element
    var alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show';
    alertDiv.setAttribute('role', 'alert');
    alertDiv.innerHTML = '<strong>Successful!</strong> You are logged in successfully as admin.' +
      '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
      '<span aria-hidden="true">&times;</span>' +
      '</button>';
  
    // Style the alert to be on top and with a width of 70vh
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '70px';
    alertDiv.style.left = '50%';
    alertDiv.style.transform = 'translateX(-50%)';
    alertDiv.style.zIndex = '1100';
    alertDiv.style.width = '120vh';
  
    // Append the alert to the body
    document.body.appendChild(alertDiv);
  
    // Remove the alert after 2 seconds
    setTimeout(function() {
      alertDiv.classList.remove('show');
      alertDiv.classList.add('fade');
      setTimeout(function() {
        alertDiv.parentNode.removeChild(alertDiv);
      }, 150);
    }, 1000);
  }
  
  // Show the alert when the page loads
  window.onload = showAlert;
  
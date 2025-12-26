// app.js

// Import Bootstrap's JS (Bootstrap's JS requires jQuery and Popper.js, included by default)
import 'bootstrap/dist/css/bootstrap.min.css';
// import 'bootstrap/dist/js/bootstrap.bundle.min.js';
// import 'bootstrap';

// Import Select2 (CSS and JS)
import 'select2/dist/js/select2.full.min.js';  // Full Select2 bundle
import 'select2/dist/css/select2.min.css';     // CSS for Select2

// Import FontAwesome icons
import '@fortawesome/fontawesome-free/css/all.css';

import './public/assets/note/noteBuilder.css';


// Ensure jQuery is available for other libraries
import $ from 'jquery';

// Initialize Select2 on all <select> elements
$(document).ready(function () {
    $('select').select2(); // Apply Select2 to all <select> elements
});

let slideIndices = {};

function plusSlides(n, id) {
    showSlides(slideIndices[id] += n, id);
}

function showSlides(n, id) {
    let i;
    let slides = document.getElementsByClassName(`mySlides-${id}`);
    if (n > slides.length) {slideIndices[id] = 1}    
    if (n < 1) {slideIndices[id] = slides.length}
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";  
    }
    slides[slideIndices[id]-1].style.display = "block";  
}

function showSlidesAuto(id) {
    let i;
    let slides = document.getElementsByClassName(`mySlides-${id}`);
    if (slides.length == 0) {
        return;
    }
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";  
    }
    slideIndices[id]++;
    if (slideIndices[id] > slides.length) {slideIndices[id] = 1}
    slides[slideIndices[id]-1].style.display = "block";
    setTimeout(() => showSlidesAuto(id), 5000); // Change image every 5 seconds
}


// Function to open the modal
function openModal() {
    var modal = document.getElementById('loginModal');
    modal.style.display = 'block';
}

// Function to close the modal
function closeModal() {
    var modal = document.getElementById('loginModal');
    modal.style.display = 'none';
}

// Event listener to open modal when login link is clicked
// script.js

document.addEventListener('DOMContentLoaded', function() {
    var loginLink = document.getElementById('loginLink');
    if (loginLink) {
        loginLink.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default link behavior
            openModal();
        });
    }
});


// Close the modal if the user clicks outside of it
window.onclick = function(event) {
    var modal = document.getElementById('loginModal');
    if (event.target == modal) {
        closeModal();
    }
}
// Function to open modal or popup
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
    var closeBtn = document.querySelector(`#${modalId} .close`);
    var popup = document.querySelector(`#${modalId} .popup`);
    if (closeBtn && popup) {
        // Position popup next to close button
        var closeBtnRect = closeBtn.getBoundingClientRect();
        popup.style.top = closeBtnRect.bottom + 10 + 'px'; // Adjust vertical position as needed
        popup.style.left = closeBtnRect.right - popup.offsetWidth + 'px'; // Adjust horizontal position as needed
        popup.classList.add('active');
    }
}

// Function to close modal or popup
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    var popup = document.querySelector(`#${modalId} .popup`);
    if (popup) {
        popup.classList.remove('active');
    }
}

// Event listeners to open/close modal or popup
document.addEventListener('DOMContentLoaded', function() {
    var loginLink = document.getElementById('loginLink');
    if (loginLink) {
        loginLink.addEventListener('click', function(event) {
            event.preventDefault();
            openModal('loginModal');
        });
    }

    var signupLink = document.getElementById('signupLink');
    if (signupLink) {
        signupLink.addEventListener('click', function(event) {
            event.preventDefault();
            openModal('signupModal');
        });
    }

    var closeBtns = document.querySelectorAll('.close');
    if (closeBtns) {
        closeBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var modalId = btn.closest('.modal').id;
                closeModal(modalId);
            });
        });
    }
});

// Close modal if user clicks outside of it
window.onclick = function(event) {
    var modals = document.querySelectorAll('.modal');
    modals.forEach(function(modal) {
        if (event.target === modal) {
            var modalId = modal.id;
            closeModal(modalId);
        }
    });
};


var clrform = document.getElementById("bookingForm");
clrform.reset();

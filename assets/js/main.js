$(function() {
    $(".comment-content").flexible();
    $(".post-description").flexible();
    $(".post-summary").flexible();
    $(".new-comment").flexible();
});

//btnNumber 1 = Log in , btnNumber 2 = Sign up
function checkPressed(btnNumber) {

    var formOverlay = document.getElementById("overlay");
    var loginForm = document.getElementById("login-form-container");
    var signupForm = document.getElementById("signup-form-container");

    if (btnNumber == 1) {
        formOverlay.className += ' show';
        loginForm.className += ' show';
    } else {
        formOverlay.className += ' show';
        signupForm.className += ' show';
    }
}

function validateSignUpForm() {
    var nameSignup = document.getElementById("nameSignup");
    var emailSignup = document.getElementById("emailSignup");
    var emailConfirm = document.getElementById("emailConfirm");
    var passwordSignup = document.getElementById("passwordSignup");
    var passwordConfirm = document.getElementById("passwordConfirm");

    if (nameSignup.value == null || nameSignup.value == '') {
        return false;
    }
    if (emailSignup.value == null || emailSignup.value == '') {
        return false;
    }
    if (emailConfirm.value == null || emailConfirm.value == '' || emailConfirm.value !== emailSignup.value) {
        return false;
    }

    if (passwordSignup.value == null || passwordSignup.value == '') {
        return false;
    }
    if (passwordConfirm.value == null || passwordConfirm.value == '' || passwordConfirm.value !== passwordSignup.value) {
        return false;
    }




    return true;
}

function validate(input, errorSpan) {

    var inputValue = document.getElementById(input.id).value;
    var errorValue = document.getElementById(errorSpan);



    if (input.id == "emailConfirm" && inputValue !== document.getElementById("emailSignup").value) {
        document.getElementById("errorEmailConfirm").className += " show";
        document.getElementById("errorEmailConfirm").innerHTML = "Email does not match";
    } else {
        document.getElementById("errorEmailConfirm").className = "error";
        document.getElementById("errorEmailConfirm").innerHTML = "";
    }

    if (input.id == "passwordConfirm" && inputValue !== document.getElementById("passwordSignup").value) {
        document.getElementById("errorPasswordConfirm").className += " show";
        document.getElementById("errorPasswordConfirm").innerHTML = "Password does not match";
    } else {
        document.getElementById("errorPasswordConfirm").className = "error";
        document.getElementById("errorPasswordConfirm").innerHTML = "";
    }

    if (inputValue == null || inputValue == '') {
        errorValue.className += " show";
        errorValue.innerHTML = "Please fill this field";
    }
}

function checkPressedDiv(event) {

    var formOverlay = document.getElementById("overlay");
    var loginFormContainer = document.getElementById("login-form-container");
    var loginForm = document.getElementById("login-form");
    var signupFormContainer = document.getElementById("signup-form-container");
    var signupForm = document.getElementById("signup-form");
    var profileSidebar = document.getElementById("profile-sidebar");

    if (event.target == document.getElementById("overlay")) {
        formOverlay.className = 'overlay';
        loginFormContainer.className = 'login-form-container';
        loginForm.reset();
        signupFormContainer.className = 'signup-form-container';
        signupForm.reset();
    }
    if ((event.target == document.getElementById("profile-sidebar")) || (event.target == document.getElementById("user-name")) || (event.target == document.getElementById("sidebar-posts-container")) || (event.target == document.getElementById("sidebar-post")) || (event.target == document.getElementById("sidebar-post-body")) || (event.target == document.getElementById("sidebar-comments-section")) || (event.target == document.getElementById("sidebar-likes-section"))) {
        profileSidebar.className = 'profile-sidebar show';
    } else {
        profileSidebar.className = 'profile-sidebar hide';
    }
}

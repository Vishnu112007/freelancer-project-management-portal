document.addEventListener("DOMContentLoaded", function () {

    const registerForm =
        document.getElementById("registerForm");

    const loginForm =
        document.getElementById("loginForm");


    if (registerForm) {

        registerForm.addEventListener("submit", function (event) {

            const password =
                registerForm.querySelector(
                    'input[name="password"]'
                );

            if (password.value.length < 6) {

                alert(
                    "Password must contain at least 6 characters."
                );

                event.preventDefault();
            }

        });

    }


    if (loginForm) {

        loginForm.addEventListener("submit", function (event) {

            const email =
                loginForm.querySelector(
                    'input[name="email"]'
                );

            const password =
                loginForm.querySelector(
                    'input[name="password"]'
                );


            if (email.value.trim() === "" ||
                password.value.trim() === "") {

                alert("Please fill all required fields.");

                event.preventDefault();
            }

        });

    }

});
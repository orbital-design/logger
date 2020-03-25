<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/templates/page-mypage.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Logger
 * @since   Logger 1.0
 *
 */

    // namespace Logger;


$form = '<div class="login-wrapper">
    <div class="login-wrapper__col">
        <figure>
            <img src="' . get_template_directory_uri() . '/assets/dist/img/orbital-login-screen-hand-image.jpg\'" />
        </figure>
    </div>

    <div class="login-wrapper__col">

        <a class="login__logo" href="https://logger.local">
            <svg width="202" height="55" viewBox="0 0 202 55" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.494 16.013C8.56 16.013 0 24.576 0 35.506 0 46.436 8.56 55 19.494 55c10.928 0 19.493-8.564 19.493-19.494s-8.56-19.493-19.493-19.493zm-.026 28.959c-5.198 0-9.423-4.246-9.423-9.46 0-5.22 4.23-9.461 9.423-9.461 5.2 0 9.424 4.246 9.424 9.46 0 5.22-4.225 9.46-9.424 9.46zm33.385-28.21H42.468v36.846h10.385V36.995c0-6.754 5.212-9.15 9.672-9.15.512 0 1.024.031 1.526.092V16.013c-4.044.08-11.208 1.372-11.208 5.822h.01v-5.073zM115.222 0c-3.586 0-6.614 3.027-6.614 6.611 0 3.584 3.028 6.617 6.614 6.617 3.585 0 6.614-3.027 6.614-6.617 0-3.584-3.029-6.611-6.614-6.611zm5.221 16.709H110v36.899h10.443v-36.9zm19.356.468V6.962l-10.519 3.085v7.125h-6.052v9.841h6.052v13.483c0 4.696.915 7.835 2.871 9.872 2.125 2.211 5.529 3.24 10.718 3.24 1.423 0 3.008-.08 4.721-.244V44.1c-.829.044-1.515.064-2.119.064-2.943 0-5.666-.442-5.666-3.666v-13.48h7.79v-9.841h-7.796zm51.657 36.431h10.443V0l-10.443 3.163v50.445zM88.934 15.734c-4.675 0-8.343 1.484-10.908 4.411l-.718.817V2.09L66.835 5.242v47.874l11.05-3.404-.572-.637.718.817c2.565 2.928 6.233 4.412 10.908 4.412 4.684 0 9.086-1.97 12.389-5.544 3.344-3.625 5.191-8.502 5.191-13.741 0-5.239-1.842-10.116-5.191-13.74-3.308-3.575-7.71-5.545-12.394-5.545zm-2.262 28.65c-5.21 0-9.445-4.202-9.445-9.36 0-5.159 4.24-9.36 9.445-9.36 5.211 0 9.446 4.201 9.446 9.36 0 5.158-4.24 9.36-9.446 9.36zm79.202-28.371c-4.686 0-9.088 1.955-12.392 5.504-3.349 3.598-5.191 8.44-5.191 13.641 0 5.2 1.842 10.043 5.191 13.642 3.304 3.548 7.706 5.504 12.392 5.504 4.67 0 8.343-1.473 10.908-4.38l.719-.811-.622.632 11.096 3.38V17.052h-10.474v4.15l-.719-.81c-2.565-2.907-6.233-4.38-10.908-4.38zm11.693 19.15c0 5.126-4.24 9.292-9.447 9.292-5.211 0-9.446-4.17-9.446-9.292 0-5.12 4.24-9.291 9.446-9.291 5.207 0 9.447 4.165 9.447 9.291z" fill="#fff"/></svg>
        </a>

        <form name="loginform" id="loginform" class="login-form" action="https://logger.local/bridge/access" method="post">
            <div class="login-form__field-wrap">
                <input type="text" name="log" id="user_login" class="login-form__input" size="20" autocapitalize="off" placeholder="Username or Email Address" />
                <label class="login-form__label" for="user_login">username/email</label>
            </div>

            <div class="login-form__field-wrap user-pass-wrap">
                <input type="password" name="pwd" id="user_pass" class="login-form__input" size="20" placeholder="Password" />
                <label class="login-form__label" for="user_pass">password</label>
                <button type="button" class="login-form__button wp-hide-pw" data-toggle="0" aria-label="Show password"></button>
            </div>

            <div class="form-split">
                <div class="forgetmenot">
                    <input name="rememberme" type="checkbox" id="rememberme" value="forever" />
                    <label for="rememberme">remember me</label>
                </div>

                <div class="submit">
                    <input type="submit" name="wp-submit" id="wp-submit" class="login-form__submit" value="log in" />
                    <input type="hidden" name="redirect_to" value="https://logger.local/bridge/wp-admin/" />
                    <input type="hidden" name="testcookie" value="1" />
                </div>
            </div>
        </form>

        <a class="login-form__link" href="https://logger.local/bridge/access?action=lostpassword">forgotten your password?</a>
    </div>
</div>';

return $form;
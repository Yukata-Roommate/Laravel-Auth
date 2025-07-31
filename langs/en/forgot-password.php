<?php

return [
    "success" => [
        "send-mail" => "Password reset email sent.",
        "reset"     => "You have successfully reset your password.",
    ],

    "failure" => [
        "issue-token"   => "Failed to issue password reset token.",
        "send-mail"     => "Failed to send password reset email.",
        "find-token"    => "Failed to find password reset token.",
        "expired-token" => "Password reset token has expired.",
    ],

    "email" => [
        "subject" => "Forgot Password",

        "message" => [
            "remarks" => "You have requested to reset your password.\nPlease enter your token in the form to complete the reset process.\nIf the expiration date has passed, please start over again.",
            "expired" => "Expired At",
            "token"   => "Token",
        ],
    ],
];

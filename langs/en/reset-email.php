<?php

return [
    "success" => [
        "send-mail" => "Reset email sent.",
        "reset"     => "You have successfully reset your email address.",
    ],

    "failure" => [
        "issue-token"   => "Failed to issue reset email token.",
        "send-mail"     => "Failed to send reset email.",
        "find-token"    => "Failed to find reset email token.",
        "expired-token" => "Reset email token has expired.",
        "reset"         => "Failed to reset email address.",
    ],

    "email" => [
        "subject" => "Reset Email",

        "message" => [
            "remarks" => "You have requested to reset your email address.\nPlease enter your token in the form to complete the reset process.\nIf the expiration date has passed, please start over again.",
            "expired" => "Expired At",
            "token"   => "Token",
        ],
    ],
];

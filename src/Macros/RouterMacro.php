<?php

namespace YukataRm\Laravel\Auth\Macros;

use Illuminate\Routing\Router;
use App\Http\Controllers\Auth;

/**
 * Router Macro
 *
 * @package YukataRm\Laravel\Auth\Macros
 *
 * @method \Illuminate\Routing\RouteRegistrar group(array $attributes, \Closure $routes)
 * @method \Illuminate\Routing\RouteRegistrar controller(string $controller)
 * @method \Illuminate\Routing\Route get(string $uri, array|string|callable|null $action = null)
 * @method \Illuminate\Routing\Route post(string $uri, array|string|callable|null $action = null)
 * @see \Illuminate\Routing\Router
 */
class RouterMacro
{
    /**
     * macro class
     *
     * @return string
     */
    public function class(): string
    {
        return Router::class;
    }

    /**
     * registered methods
     *
     * @return array<string, \Closure>
     */
    public function methods(): array
    {
        return [
            "login"          => $this->login(),
            "logout"         => $this->logout(),
            "resetEmail"     => $this->resetEmail(),
            "resetPassword"  => $this->resetPassword(),
            "forgotPassword" => $this->forgotPassword(),
            "auth"           => function (): void {
                $this->login();
                $this->logout();
                $this->resetEmail();
                $this->resetPassword();
                $this->forgotPassword();
            },
        ];
    }

    /**
     * get router login macro
     *
     * @return \Closure
     */
    protected function login(): \Closure
    {
        return function (): void {
            $this->group(["prefix" => "auth", "as" => "auth."], function () {
                $this->controller(Auth\LoginController::class)
                    ->prefix("login")
                    ->as("login.")
                    ->group(function () {
                        $this->get("/", "form")->name("form");
                        $this->post("/", "handle")->name("handle");
                    });
            });
        };
    }

    /**
     * get router logout macro
     *
     * @return \Closure
     */
    protected function logout(): \Closure
    {
        return function (): void {
            $this->group(["prefix" => "auth", "as" => "auth."], function () {
                $this->controller(Auth\LogoutController::class)
                    ->prefix("logout")
                    ->as("logout.")
                    ->group(function () {
                        $this->post("/", "handle")->name("handle");
                    });
            });
        };
    }

    /**
     * get router resetEmail macro
     *
     * @return \Closure
     */
    protected function resetEmail(): \Closure
    {
        return function (): void {
            $this->group(["prefix" => "auth", "as" => "auth."], function () {
                $this->controller(Auth\ResetEmailController::class)
                    ->prefix("reset-email")
                    ->as("reset-email.")
                    ->group(function () {
                        $this->get("/", "form")->name("form");
                        $this->post("/", "handle")->name("handle");
                    });

                $this->controller(Auth\ResetEmailTokenController::class)
                    ->prefix("reset-email-token")
                    ->as("reset-email-token.")
                    ->group(function () {
                        $this->get("/", "form")->name("form");
                        $this->post("/", "handle")->name("handle");
                    });
            });
        };
    }

    /**
     * get router resetPassword macro
     *
     * @return \Closure
     */
    protected function resetPassword(): \Closure
    {
        return function (): void {
            $this->group(["prefix" => "auth", "as" => "auth."], function () {
                $this->controller(Auth\ResetPasswordController::class)
                    ->prefix("reset-password")
                    ->as("reset-password.")
                    ->group(function () {
                        $this->get("/", "form")->name("form");
                        $this->post("/", "handle")->name("handle");
                    });
            });
        };
    }

    /**
     * get router forgotPassword macro
     *
     * @return \Closure
     */
    protected function forgotPassword(): \Closure
    {
        return function (): void {
            $this->group(["prefix" => "auth", "as" => "auth."], function () {
                $this->controller(Auth\ForgotPasswordController::class)
                    ->prefix("forgot-password")
                    ->as("forgot-password.")
                    ->group(function () {
                        $this->get("/", "form")->name("form");
                        $this->post("/", "handle")->name("handle");
                    });

                $this->controller(Auth\ForgotPasswordTokenController::class)
                    ->prefix("forgot-password-token")
                    ->as("forgot-password-token.")
                    ->group(function () {
                        $this->get("/", "form")->name("form");
                        $this->post("/", "handle")->name("handle");
                    });
            });
        };
    }
}

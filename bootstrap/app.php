<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
            'ensure-password-changed' => \App\Http\Middleware\EnsurePasswordChanged::class,
        ]);

        // Log users off after 15 minutes of inactivity.
        $middleware->append(\App\Http\Middleware\CheckSessionIdleTimeout::class);

        // Laravel's built-in ValidatePostSize runs as global middleware BEFORE
        // StartSession, so when it throws there's no session and flashed data
        // is lost. Replace it with our own middleware inside the 'web' group
        // (after StartSession) so $request->session() is available when the
        // exception renders, letting us persist flashed data + session cookie.
        $middleware->remove(\Illuminate\Http\Middleware\ValidatePostSize::class);
        $middleware->web(append: [
            \App\Http\Middleware\ValidatePostSize::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Treat an oversized upload like a failed validation rule: redirect back
        // with input flashed and a "file" error so the upload form shows it the
        // same way a missed mandatory field is shown (@error('file')), instead
        // of dumping the Symfony stack trace.
        $renderOversizeUpload = function ($request) use ($exceptions) {
            $session = $request->session();

            $response = back()
                ->withInput()                              // keeps typed fields filled on refresh
                ->withErrors(['file' => 'The uploaded file is too large. The maximum allowed size is 10MB.'])  // inline @error('file') state
                ->with('error', 'The uploaded file is too large. The maximum allowed size is 10MB.');          // popup (flashModal) the user clicks OK on

            // StartSession::saveSession() never ran (it sits after $next(),
            // which never ran when the exception was thrown). Persist flashed
            // data to the session manually:
            $session->save();

            // Encrypt the cookie the same way EncryptCookies::encrypt() does,
            // since its terminate() also never ran when the exception was
            // thrown. Without this, decryption fails on the next request
            // (419 Page Expired / logged out).
            $encrypter = app('encrypter');
            $encryptedValue = $encrypter->encrypt(
                \Illuminate\Cookie\CookieValuePrefix::create($session->getName(), $encrypter->getKey()) . $session->getId(),
                false
            );

            $config = app('config')->get('session', []);
            $response->headers->setCookie(new \Symfony\Component\HttpFoundation\Cookie(
                $session->getName(),
                $encryptedValue,
                ! empty($config['expire_on_close']) ? 0 : (time() + ($config['lifetime'] ?? 120) * 60),
                $config['path'] ?? '/',
                $config['domain'] ?? '',
                $config['secure'] ?? false,
                $config['http_only'] ?? true,
                false,
                $config['same_site'] ?? null,
                $config['partitioned'] ?? false
            ));

            return $response;
        };

        // PostTooLargeException: thrown by our ValidatePostSize middleware
        // (in the web group, after StartSession) when content exceeds
        // post_max_size. StartSession already ran, so $request->session()
        // works and flashed data survives.
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, $request) use ($renderOversizeUpload) {
            return $renderOversizeUpload($request);
        });

        // TokenMismatchException (419 Page Expired): when PHP rejects an
        // oversized POST, $_POST is empty and ValidateCsrfToken throws
        // TokenMismatchException before our ValidatePostSize middleware ever
        // runs. Laravel's prepareException() converts it to HttpException(419),
        // so we catch HttpException here and match status code 419 instead.
        $exceptions->render(function (HttpException $e, $request) use ($renderOversizeUpload) {
            if ($e->getStatusCode() === 419) {
                return $renderOversizeUpload($request);
            }

            return null;
        });
    })->create();

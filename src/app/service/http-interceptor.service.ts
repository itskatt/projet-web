import {
    HttpErrorResponse,
    HttpEvent,
    HttpHandler,
    HttpInterceptor,
    HttpRequest,
} from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { catchError, Observable, throwError } from 'rxjs';

@Injectable({
    providedIn: 'root',
})
export class HttpInterceptorService implements HttpInterceptor {
    constructor(private router: Router) {}
    intercept(
        req: HttpRequest<any>,
        next: HttpHandler
    ): Observable<HttpEvent<any>> {
        return next.handle(req.clone({ withCredentials: true })).pipe(
            catchError((error: HttpErrorResponse) => {
                switch (error.status) {
                    case 401: // Unauthorised
                        this.router.navigateByUrl('/login');
                        break;
                }

                // Send to other handler
                return throwError(() => error);
            })
        );
    }
}

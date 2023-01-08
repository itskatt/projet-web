import {
    HttpClient,
    HttpErrorResponse,
    HttpHeaders,
} from '@angular/common/http';
import { Injectable } from '@angular/core';
import { catchError, map, Observable, of, throwError } from 'rxjs';
import {
    Article,
    ArticleResponse,
    ArticlesResponse,
    CartInvoicesResponse,
    Invoice,
    LoginResponse,
    PreviousInvoiceResponse as PreviousCartResponse,
} from '../shared/interfaces';

@Injectable({
    providedIn: 'root',
})
export class HttpClientService {
    private base: string = 'http://localhost/api/';

    constructor(private http: HttpClient) {}

    getArticles(): Observable<Article[]> {
        return this.http
            .get<ArticlesResponse>(
                this.base + 'public/articles.php?page=1'
            ) /* TODO les pages */
            .pipe(
                map((data: ArticlesResponse) => data.articles),

                catchError((error: HttpErrorResponse) => {
                    console.error(error);
                    return of([]);
                })
            );
    }

    getArticle(id: number): Observable<Article> {
        return this.http
            .get<ArticleResponse>(this.base + 'public/article.php?id=' + id)
            .pipe(map((data: ArticleResponse) => data.article));
    }

    login(
        email: string,
        password: string,
        rememberMe: boolean
    ): Observable<LoginResponse> {
        return this.http.post<LoginResponse>(
            this.base + 'user/login.php',
            {
                email: email,
                password: password,
                remember: rememberMe,
            },
            { withCredentials: true }
        );
    }

    logout(): Observable<any> {
        return this.http.post(
            this.base + 'user/logout.php',
            {},
            { withCredentials: true }
        );
    }

    getInvoices(): Observable<Invoice[]> {
        return this.http
            .get<CartInvoicesResponse>(this.base + 'cart/invoices.php')
            .pipe(map((data: CartInvoicesResponse) => data.invoices));
    }

    getPreviousCart(id: number): Observable<PreviousCartResponse> {
        return this.http.get<PreviousCartResponse>(
            this.base + 'cart/previous.php?id=' + id
        );
    }
}

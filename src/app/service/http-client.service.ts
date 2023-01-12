import {
    HttpClient,
    HttpErrorResponse
} from "@angular/common/http";
import { Injectable } from "@angular/core";
import { catchError, map, Observable, of, throwError } from "rxjs";
import {
    AdminStatsResponse,
    Article,
    ArticleResponse,
    ArticlesResponse,
    ArticleStockUpdateStatement,
    CartInvoicesResponse,
    CartUpdateStatement,
    CurrentCartResponse,
    Invoice,
    LoginResponse,
    PreviousInvoiceResponse as PreviousCartResponse,
    SingleArticle,
    Statusable,
} from "../shared/interfaces";

@Injectable({
    providedIn: "root",
})
export class HttpClientService {
    private base: string = "http://localhost/api/";

    constructor(private http: HttpClient) {}

    getArticles(): Observable<Article[]> {
        return this.http
            .get<ArticlesResponse>(
                this.base + "public/articles.php?page=1"
            ) /* TODO les pages */
            .pipe(
                map((data: ArticlesResponse) => data.articles),

                catchError((error: HttpErrorResponse) => {
                    console.error(error);
                    return of([]);
                })
            );
    }

    getArticle(id: number): Observable<SingleArticle> {
        return this.http
            .get<ArticleResponse>(this.base + "public/article.php?id=" + id)
            .pipe(map((data: ArticleResponse) => data.article));
    }

    register(
        email: string,
        lastName: string,
        firstName: string,
        password: string
    ): Observable<any> {
        return this.http.post(this.base + "user/register.php", {
            email: email,
            last_name: lastName,
            first_name: firstName,
            password: password
        });
    }

    login(
        email: string,
        password: string,
        rememberMe: boolean
    ): Observable<LoginResponse> {
        return this.http.post<LoginResponse>(this.base + "user/login.php", {
            email: email,
            password: password,
            remember: rememberMe,
        });
    }

    logout(): Observable<any> {
        return this.http.post(this.base + "user/logout.php", {});
    }

    getInvoices(): Observable<Invoice[]> {
        return this.http
            .get<CartInvoicesResponse>(this.base + "cart/invoices.php")
            .pipe(map((data: CartInvoicesResponse) => data.invoices));
    }

    getPreviousCart(id: number): Observable<PreviousCartResponse> {
        return this.http.get<PreviousCartResponse>(
            this.base + "cart/previous.php?id=" + id
        );
    }

    getCurrentCart(): Observable<CurrentCartResponse> {
        return this.http.get<CurrentCartResponse>(
            this.base + "cart/current.php"
        );
    }

    deleteCurrentCart(): Observable<any> {
        return this.http.delete(this.base + "cart/delete.php");
    }

    createCart(): Observable<any> {
        return this.http.post(this.base + "cart/create.php", {});
    }

    updateCart(toUpdate: CartUpdateStatement[]): Observable<any> {
        return this.http.put(this.base + "cart/update.php", toUpdate);
    }

    getAdminStats(): Observable<AdminStatsResponse> {
        return this.http.get<AdminStatsResponse>(this.base + "admin/stats.php");
    }

    updateArticleStock(toUpdate: ArticleStockUpdateStatement): Observable<Statusable> {
        return this.http.put<Statusable>(this.base + "admin/article.php", toUpdate);
    }
}

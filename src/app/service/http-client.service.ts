import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { catchError, map, Observable, of } from 'rxjs';
import { Article, ArticleResponse, ArticlesResponse, Statusable } from '../shared/interfaces';

@Injectable({
    providedIn: 'root'
})
export class HttpClientService {
    private base: string = "http://localhost/api/";

    constructor(private http: HttpClient) { }

    private handleError<T>(result?: T) {
        return (error: HttpErrorResponse): Observable<T> => {

            console.group("Erreur");
            console.error(error.status);
            console.error(error.error.message);
            console.groupEnd();

            return of(result as T);
        };
    }

    getArticles(): Observable<Article[]> {
        return this.http.get<ArticlesResponse>(this.base + "public/articles.php?page=1") /* TODO les pages */
            .pipe(
                map((data: ArticlesResponse) => data.articles),
                catchError(this.handleError<Article[]>([]))
            );
    }

    getArticle(id: number): Observable<Article> {
        return this.http.get<ArticleResponse>(this.base + "public/article.php?id=" + id)
            .pipe(
                map((data: ArticleResponse) => data.article),
                catchError(this.handleError<Article>())
            );
    }
}

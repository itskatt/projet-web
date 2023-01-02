import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { catchError, map, Observable, of } from 'rxjs';
import { Article, ArticlesResponse, Statusable } from '../interfaces';

@Injectable({
    providedIn: 'root'
})
export class HttpClientService {
    private base: string = "http://localhost/api/";

    constructor(private http: HttpClient) { }

    getArticles(): Observable<Article[]> {
        return this.http.get<ArticlesResponse>(this.base + "public/articles.php?page=1") /* TODO les pages */
            .pipe(
                map((data: ArticlesResponse) => data.articles),
                catchError(this.handleError<Article[]>([]))
            );
    }

    private handleError<T>(result?: T) {
        return (error: any): Observable<T> => {

            // TODO: send the error to remote logging infrastructure
            console.error(error); // log to console instead

            // Let the app keep running by returning an empty result.
            return of(result as T);
        };
    }
}

import { Component, OnInit } from '@angular/core';
import { Article } from '../shared/interfaces';
import { HttpClientService } from '../service/http-client.service';

@Component({
    selector: 'app-articles-grid',
    templateUrl: './articles-grid.component.html',
    styleUrls: ['./articles-grid.component.css'],
})
export class ArticlesGridComponent implements OnInit {
    articles: Article[] = [];

    constructor(private client: HttpClientService) {}

    ngOnInit(): void {
        this.fetchArticles();
    }

    fetchArticles(): void {
        this.client.getArticles().subscribe((articles) => {
            this.articles = articles;
        });
    }

    filledStars(n: number): Array<number> {
        return Array(n);
    }

    emptyStars(n: number): Array<number> {
        return Array(5 - n);
    }
}

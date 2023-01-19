import { Component, OnInit } from "@angular/core";
import { Article } from "../shared/interfaces";
import { HttpClientService } from "../service/http-client.service";
import { ArticleUser } from "../shared/article-user";

@Component({
    selector: "app-articles-grid",
    templateUrl: "./articles-grid.component.html",
    styleUrls: ["./articles-grid.component.css"],
})
export class ArticlesGridComponent extends ArticleUser implements OnInit {
    len: number = 40;

    articles: Article[] = [];

    constructor(private client: HttpClientService) {
        super();
    }

    ngOnInit(): void {
        this.fetchArticles();
    }

    fetchArticles(): void {
        this.client.getArticles(true).subscribe((articles) => {
            this.articles = articles;
        });
    }

    trunc(text: string): string {
        if (text.length > this.len) {
            return text.substring(0, this.len) + "…";
        }

        return text;
    }
}

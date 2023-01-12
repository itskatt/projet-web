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
    articles: Article[] = [];

    constructor(private client: HttpClientService) {
        super();
    }

    ngOnInit(): void {
        this.fetchArticles();
    }

    fetchArticles(): void {
        this.client.getArticles().subscribe((articles) => {
            this.articles = articles;
        });
    }
}

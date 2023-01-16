import { Component, OnInit } from "@angular/core";
import { HttpClientService } from "../service/http-client.service";
import { ArticleUser } from "../shared/article-user";
import { AdminStatsResponse, Article } from "../shared/interfaces";

@Component({
    selector: "app-admin-dashboard",
    templateUrl: "./admin-dashboard.component.html",
    styleUrls: [
        "./admin-dashboard.component.css",
        "../shared/dual-ui.css",
        "../shared/button.css",
        "../shared/rounded-article-list.css",
    ],
})
export class AdminDashboardComponent extends ArticleUser implements OnInit {
    articles: Article[] = [];
    articleMap: Map<number, Article> = new Map();

    stats: AdminStatsResponse | null = null;

    constructor(private client: HttpClientService) {
        super();
    }

    ngOnInit(): void {
        this.client.getAdminStats().subscribe((stats) => {
            this.stats = stats;
        });

        this.fetchArticles();
    }

    fetchArticles(): void {
        this.client.getArticles().subscribe((articles) => {
            this.articles = articles;
            for (let article of this.articles) {
                this.articleMap.set(article.article_id, article);
            }
        });
    }

    updateArticle(id: number, event: Event): void {
        let article = this.articleMap.get(id);
        if (article == undefined) return;

        article.quantity = +(<HTMLInputElement>event.target).value;

        this.client
            .updateArticleStock({
                article_id: article.article_id,
                quantity: article.quantity,
            })
            .subscribe((_) => {});
    }

    deleteArticle(id: number): void {
        this.client.deleteArticle(id).subscribe((_) => {
            this.fetchArticles();
        });
    }

    avgSalesPerInvoices(): number {
        if (this.stats == null) return -1;
        return (
            this.stats.sales_per_invoice
                .map((invoice) => invoice.sales)
                .reduce((a, b) => a + b) / this.stats.sales_per_invoice.length
        );
    }
}

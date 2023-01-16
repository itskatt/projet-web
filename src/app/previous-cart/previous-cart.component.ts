import { Component, OnInit } from "@angular/core";
import { ActivatedRoute } from "@angular/router";
import { HttpClientService } from "../service/http-client.service";
import { ArticleUser } from "../shared/article-user";
import { SoldArticle } from "../shared/interfaces";

@Component({
    selector: "app-previous-cart",
    templateUrl: "./previous-cart.component.html",
    styleUrls: [
        "./previous-cart.component.css",
        "../shared/rounded-article-list.css",
    ],
})
export class PreviousCartComponent extends ArticleUser implements OnInit {
    articles: SoldArticle[] = [];
    priceNoTax: number = -1;
    priceTax: number = -1;
    invoiceId: number = -1;
    numArticles: number | string = -1;

    constructor(
        private client: HttpClientService,
        private route: ActivatedRoute
    ) {
        super();
    }

    ngOnInit(): void {
        const id = parseInt(this.route.snapshot.paramMap.get("id")!, 10);
        this.client.getPreviousCart(id).subscribe((invoice) => {
            this.articles = invoice.articles;
            this.priceNoTax = invoice.price_no_tax;
            this.priceTax = invoice.price_tax;
            this.invoiceId = invoice.invoice_id;
            this.numArticles = invoice.num_articles;
        });
    }
}

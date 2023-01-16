import { HttpErrorResponse } from "@angular/common/http";
import { Component, OnInit } from "@angular/core";
import { CurrentUserService } from "../service/current-user.service";
import { HttpClientService } from "../service/http-client.service";
import { ArticleUser } from "../shared/article-user";
import {
    CartArticle,
    CartUpdateStatement,
    Invoice,
} from "../shared/interfaces";

@Component({
    selector: "app-cart",
    templateUrl: "./cart.component.html",
    styleUrls: [
        "./cart.component.css",
        "../shared/dual-ui.css",
        "../shared/button.css",
        "../shared/rounded-article-list.css"
    ],
})
export class CartComponent extends ArticleUser implements OnInit {
    invoices: Invoice[] = [];

    cartArticles: CartArticle[] = [];
    cartPrice: number = -1;
    cartPriceNoTax: number = -1;

    updateHappened: boolean = false;

    articleMap: Map<number, CartArticle> = new Map();

    constructor(
        private client: HttpClientService,
        protected user: CurrentUserService
    ) {
        super();
    }

    ngOnInit(): void {
        this.client
            .getInvoices()
            .subscribe((invoices) => (this.invoices = invoices));

        this.fetchCart();
    }

    fetchCart(): void {
        this.client.getCurrentCart().subscribe({
            next: (cart) => {
                this.cartArticles = cart.articles;
                this.cartPrice = +cart.price_tax.toFixed(2);
                this.cartPriceNoTax = +cart.price_no_tax.toFixed(2);

                for (let article of this.cartArticles) {
                    this.articleMap.set(article.article_id, article);
                }
            },
            error: (error: Error) => {
                if (error instanceof HttpErrorResponse) {
                    if (error.status === 404) {
                        // Le panier n'existe pas
                        return;
                    }
                }

                throw error;
            },
        });
    }

    formatDate(date: string): string {
        return new Date(date).toLocaleDateString("fr-FR", {
            weekday: "long",
            day: "numeric",
            month: "long",
            year: "numeric",
            hour: "numeric",
            minute: "numeric",
        });
    }

    deleteCart(): void {
        this.client.deleteCurrentCart().subscribe((_) => {
            this.cartPrice = -1;
            this.cartArticles = [];
        });
    }

    createCart(): void {
        this.client.createCart().subscribe((_) => {
            this.cartPrice = 0;
        });
    }

    updateCart(): void {
        let toUpdate: CartUpdateStatement[] = [];

        for (let article of this.cartArticles) {
            toUpdate.push({
                article_id: article.article_id,
                quantity: article.cart_quantity,
            });
        }

        this.client.updateCart(toUpdate).subscribe((_) => {
            this.updateHappened = false;
            this.fetchCart();
        });
    }

    handleArticleScroll(event: WheelEvent, id: number): void {
        let action: "add" | "sub" = event.deltaY < 0 ? "add" : "sub";
        this.updateArticle(id, action);
        event.preventDefault();
    }

    updateArticle(id: number, action: "add" | "sub"): void {
        let article = this.articleMap.get(id);
        if (article == undefined) return;

        if (
            action == "add" &&
            article.cart_quantity <= article.stock_quantity
        ) {
            article.cart_quantity++;
        } else if (action == "sub" && article.cart_quantity != 0) {
            article.cart_quantity--;
        }
        this.updateHappened = true;
    }
}

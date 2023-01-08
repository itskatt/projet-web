import { HttpErrorResponse } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { CurrentUserService } from '../service/current-user.service';
import { HttpClientService } from '../service/http-client.service';
import { CartArticle, Invoice } from '../shared/interfaces';

@Component({
    selector: 'app-cart',
    templateUrl: './cart.component.html',
    styleUrls: ['./cart.component.css'],
})
export class CartComponent implements OnInit {
    invoices: Invoice[] = [];

    cartArticles: CartArticle[] = [];
    cartPrice: number = -1;
    cartPriceNoTax: number = -1;

    constructor(
        private client: HttpClientService,
        protected user: CurrentUserService
    ) {}

    ngOnInit(): void {
        this.client
            .getInvoices()
            .subscribe((invoices) => (this.invoices = invoices));

        this.client.getCurrentCart().subscribe({
            next: cart => {
                this.cartArticles = cart.articles;
                this.cartPrice = cart.price_tax;
                this.cartPriceNoTax = cart.price_no_tax;
            },
            error: (error: Error) => {
                if (error instanceof HttpErrorResponse) {
                    if (error.status === 404) {
                        console.log("Le panier n'existe pas")
                        return;
                    }
                }

                throw error;
            }
        });
    }

    formatDate(date: string): string {
        return new Date(date).toLocaleDateString('fr-FR', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
        });
    }
}

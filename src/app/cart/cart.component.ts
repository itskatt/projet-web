import { Component, OnInit } from '@angular/core';
import { CurrentUserService } from '../service/current-user.service';
import { HttpClientService } from '../service/http-client.service';
import { Invoice } from '../shared/interfaces';

@Component({
    selector: 'app-cart',
    templateUrl: './cart.component.html',
    styleUrls: ['./cart.component.css'],
})
export class CartComponent implements OnInit {
    invoices: Invoice[] = [];

    constructor(
        private client: HttpClientService,
        protected user: CurrentUserService
    ) {}

    ngOnInit(): void {
        this.client
            .getInvoices()
            .subscribe((invoices) => (this.invoices = invoices));
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

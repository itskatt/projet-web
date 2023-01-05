import { Component, OnInit } from '@angular/core';
import { HttpClientService } from '../service/http-client.service';
import { Invoice } from '../shared/interfaces';

@Component({
    selector: 'app-cart',
    templateUrl: './cart.component.html',
    styleUrls: ['./cart.component.css']
})
export class CartComponent implements OnInit {
    email: string | null = null;
    invoices: Invoice[] = [];

    constructor(private client: HttpClientService) { }

    ngOnInit(): void {
        this.email = localStorage.getItem("email")

        this.client.getInvoices().subscribe(invoices => this.invoices = invoices)
    }

}

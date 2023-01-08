import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { HttpClientService } from '../service/http-client.service';
import { Article, SoldArticle } from '../shared/interfaces';

@Component({
    selector: 'app-previous-cart',
    templateUrl: './previous-cart.component.html',
    styleUrls: ['./previous-cart.component.css']
})
export class PreviousCartComponent implements OnInit {
    articles: SoldArticle[] = [];
    priceNoTax: number = -1;
    priceTax: number = -1;

    constructor(private client: HttpClientService, private route: ActivatedRoute) {}

    ngOnInit(): void {
        const id = parseInt(this.route.snapshot.paramMap.get('id')!, 10);
        this.client.getPreviousCart(id).subscribe(invoice => {
            this.articles = invoice.articles;
            this.priceNoTax = invoice.price_no_tax;
            this.priceTax = invoice.price_tax;
        })
    }
}
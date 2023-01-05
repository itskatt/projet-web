import { Component, OnInit } from '@angular/core';

@Component({
    selector: 'app-cart',
    templateUrl: './cart.component.html',
    styleUrls: ['./cart.component.css']
})
export class CartComponent implements OnInit {
    email: string | null = null;

    ngOnInit(): void {
        this.email = localStorage.getItem("email")
    }

}

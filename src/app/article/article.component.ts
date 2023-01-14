import { HttpErrorResponse } from "@angular/common/http";
import { Component, OnInit } from "@angular/core";
import { Router, ActivatedRoute, ParamMap } from "@angular/router";
import { HttpClientService } from "../service/http-client.service";
import { ArticleUser } from "../shared/article-user";
import { CartUpdateStatement, SingleArticle } from "../shared/interfaces";

@Component({
    selector: "app-article",
    templateUrl: "./article.component.html",
    styleUrls: ["./article.component.css", "../shared/button.css"],
})
export class ArticleComponent extends ArticleUser implements OnInit {
    article: SingleArticle = {
        // Valeurs par défault
        article_id: 0,
        article_name: "",
        description: "",
        rating: 0,
        year: 0,
        price_tax: "",
        price_no_tax: "",
        image: "",
        supplier_name: "",
        quantity: 0,
    };

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private client: HttpClientService
    ) {
        super();
    }

    ngOnInit(): void {
        const id = parseInt(this.route.snapshot.paramMap.get("id")!, 10);

        // Récuperation de l'article
        this.client.getArticle(id).subscribe((article) => {
            this.article = article;
        });
    }

    addToCart(): void {
        let content: CartUpdateStatement[] = [
            {
                article_id: this.article.article_id,
                quantity: 1,
            },
        ];

        this.client.updateCart(content).subscribe({
            next: (_) => this.router.navigateByUrl("/cart"),

            // Si le panier n'existe pas encore, on le crée
            error: (error: Error) => {
                if (error instanceof HttpErrorResponse) {
                    if (error.status == 400) {
                        this.client.createCart().subscribe((_) => {
                            this.client
                                .updateCart(content)
                                .subscribe((_) =>
                                    this.router.navigateByUrl("/cart")
                                );
                        });
                        return;
                    }
                }

                throw error;
            },
        });
    }
}

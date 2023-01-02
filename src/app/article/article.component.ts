import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, ParamMap } from '@angular/router';
import { HttpClientService } from '../service/http-client.service';
import { Article } from '../shared/interfaces';

@Component({
    selector: 'app-article',
    templateUrl: './article.component.html',
    styleUrls: ['./article.component.css']
})
export class ArticleComponent implements OnInit {
    article: Article = { // Valeurs par défault
        article_id: 0,
        article_name: '',
        description: '',
        rating: 0,
        year: 0,
        price_tax: '',
        image: '',
        supplier_name: '',
        quantity: 0
    };

    constructor(
        private route: ActivatedRoute,
        private client: HttpClientService
    ) { }

    ngOnInit(): void {
        const id = parseInt(this.route.snapshot.paramMap.get("id")!, 10);
        console.log("ID de l'article : " + id)

        // Récuperation de l'article
        this.client.getArticle(id).subscribe(article => {
            this.article = article
        });
    }
}

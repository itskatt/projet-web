import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, ParamMap } from '@angular/router';

@Component({
    selector: 'app-article',
    templateUrl: './article.component.html',
    styleUrls: ['./article.component.css']
})
export class ArticleComponent implements OnInit {
    constructor(private route: ActivatedRoute) { }

    ngOnInit(): void {
        const id = parseInt(this.route.snapshot.paramMap.get("id")!, 10);
        console.log("ID de l'article : " + id)
    }
}

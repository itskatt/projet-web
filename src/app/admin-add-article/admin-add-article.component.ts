import { Component } from "@angular/core";

@Component({
    selector: "app-admin-add-article",
    templateUrl: "./admin-add-article.component.html",
    styleUrls: ["./admin-add-article.component.css", "../shared/forms.css"],
})
export class AdminAddArticleComponent {
    articleError: string = "";
}

import { Component } from "@angular/core";
import { DomSanitizer } from "@angular/platform-browser";

@Component({
    selector: "app-admin-add-article",
    templateUrl: "./admin-add-article.component.html",
    styleUrls: [
        "./admin-add-article.component.css",
        "../shared/forms.css",
        "../shared/button.css",
    ],
})
export class AdminAddArticleComponent {
    articleError: string = "";

    uploadImageUrl: any = "";

    constructor(private sanitizer: DomSanitizer) {}

    showImage(event: any): void {
        this.uploadImageUrl = this.sanitizer.bypassSecurityTrustUrl(
            URL.createObjectURL(event.target.files[0])
        );
    }

    removeImage(): void {
        this.uploadImageUrl = "";
    }
}

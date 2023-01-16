import { Component } from "@angular/core";
import { FormBuilder, FormGroup } from "@angular/forms";
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
    newArticleForm: FormGroup;
    articleError: string = "";

    uploadImageUrl: any = "";
    uploadImage: File | null = null;

    constructor(private sanitizer: DomSanitizer, private builder: FormBuilder) {
        this.newArticleForm = builder.group({
            articleName: [""],
            supplierName: [""],
            year: [],
            description: [""],
            rating: [0],
            supplierPrice: [],
            quantity: []
        });
    }

    showImage(event: any): void {
        this.uploadImageUrl = this.sanitizer.bypassSecurityTrustUrl(
            URL.createObjectURL(event.target.files[0])
        );
        this.uploadImage = event.target.files;
    }

    removeImage(): void {
        this.uploadImageUrl = "";
    }

    submit(): void {
        console.log(this.newArticleForm.value)
        // if (!this.newArticleForm.valid) {
        //     return;
        // }

        // let formData = new FormData();
    }
}

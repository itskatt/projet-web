import { HttpErrorResponse } from "@angular/common/http";
import { Component } from "@angular/core";
import { FormBuilder, FormGroup } from "@angular/forms";
import { DomSanitizer } from "@angular/platform-browser";
import { Router } from "@angular/router";
import { HttpClientService } from "../service/http-client.service";

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

    constructor(
        private sanitizer: DomSanitizer,
        builder: FormBuilder,
        private client: HttpClientService,
        private router: Router
    ) {
        this.newArticleForm = builder.group({
            articleName: [""],
            supplierName: [""],
            year: [],
            description: [""],
            rating: [0],
            supplierPrice: [],
            quantity: [],
        });
    }

    showImage(event: any): void {
        this.uploadImageUrl = this.sanitizer.bypassSecurityTrustUrl(
            URL.createObjectURL(event.target.files[0])
        );
        this.uploadImage = event.target.files[0];
    }

    removeImage(): void {
        this.uploadImageUrl = "";
    }

    submit(): void {
        if (!this.newArticleForm.valid) {
            return;
        }

        let formData = new FormData();
        formData.append("article_name", this.newArticleForm.value.articleName);
        formData.append(
            "supplier_name",
            this.newArticleForm.value.supplierName
        );
        formData.append("description", this.newArticleForm.value.description);
        formData.append("rating", this.newArticleForm.value.rating);
        formData.append("year", this.newArticleForm.value.year);
        formData.append(
            "supplier_price",
            this.newArticleForm.value.supplierPrice
        );
        formData.append("quantity", this.newArticleForm.value.supplierPrice);

        // Si nous avons une image, on l'ajoute
        if (this.uploadImage != null) {
            formData.append("upl_img", this.uploadImage, this.uploadImage.name);
        }

        this.client.createArticle(formData).subscribe({
            next: (resp) => {
                this.router.navigate(["/article/" + resp.article_id]);
            },
            error: (error: Error) => {
                if (error instanceof HttpErrorResponse) {
                    if ([400, 409].includes(error.status)) {
                        this.articleError = error.error.message;
                        return;
                    }
                }

                throw error;
            },
        });
    }
}

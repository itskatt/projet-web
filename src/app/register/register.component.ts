import { HttpErrorResponse } from "@angular/common/http";
import { Component } from "@angular/core";
import { FormControl, FormGroup } from "@angular/forms";
import { Router } from "@angular/router";
import { HttpClientService } from "../service/http-client.service";

@Component({
    selector: "app-register",
    templateUrl: "./register.component.html",
    styleUrls: ["./register.component.css", "../shared/forms.css"],
})
export class RegisterComponent {
    registerForm: FormGroup;
    registerError: string = "";

    constructor(
        private client: HttpClientService,
        private router: Router
    ) {
        this.registerForm = new FormGroup({
            email: new FormControl(""),
            lastName: new FormControl(""),
            firstName: new FormControl(""),
            password: new FormControl("")
        });
    }

    submit(): void {
        if (!this.registerForm.valid) {
            return;
        }

        this.client.register(
            this.registerForm.value.email,
            this.registerForm.value.lastName,
            this.registerForm.value.firstName,
            this.registerForm.value.password,
        ).subscribe({
            next: _ => {
                this.router.navigate(["/login"]);
            },
            error: (error: Error) => {
                if (error instanceof HttpErrorResponse) {
                    if ([400, 409].includes(error.status)) {
                        this.registerError = error.error.message;
                        return;
                    }
                }

                throw error;
            }
        });
    }
}

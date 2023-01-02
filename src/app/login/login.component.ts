import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup } from '@angular/forms';

@Component({
    selector: 'app-login',
    templateUrl: './login.component.html',
    styleUrls: ['./login.component.css', "../shared/forms.css"]
})
export class LoginComponent {
    loginForm: FormGroup;

    constructor() {
        this.loginForm = new FormGroup({
            email: new FormControl(""),
            password: new FormControl(""),
            remember: new FormControl(false)
        });
    }

    submit(): void {
        if (!this.loginForm.valid) {
            return;
        }

        console.log(this.loginForm.value.email);
        console.log(this.loginForm.value.password);
        console.log(this.loginForm.value.remember);
    }
}

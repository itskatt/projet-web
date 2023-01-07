import { Component } from '@angular/core';

@Component({
    selector: 'app-register',
    templateUrl: './register.component.html',
    styleUrls: ['./register.component.css', '../shared/forms.css'],
})
export class RegisterComponent {
    registerError: string = "";
}

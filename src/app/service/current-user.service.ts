import { Injectable } from '@angular/core';

@Injectable({
    providedIn: 'root'
})
export class CurrentUserService {

    constructor() { }

    /**
     * Renvoie True si un utilisateur est actuelement connecté.
     */
    isLoggedIn(): boolean {
        return this.email != null
    }

    get email(): string | null {
        return localStorage.getItem("email")
    }

    get lastName(): string | null {
        return localStorage.getItem("last_name")
    }

    get firstName(): string | null {
        return localStorage.getItem("first_name")
    }
}

import { Injectable } from '@angular/core';

/**
 * Service d'utilité pour l'utilisateur actuelement connecté.
 */
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

    /**
     * Déconecte l'utilisateur localement, effacant les données dans le local storage.
     */
    localLogout(): void {
        localStorage.removeItem("email")
        localStorage.removeItem("last_name")
        localStorage.removeItem("first_name")
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
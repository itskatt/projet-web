import { Component, Input } from "@angular/core";

@Component({
    selector: "app-star-rating",
    templateUrl: "./star-rating.component.html",
    styleUrls: ["./star-rating.component.css"],
})
export class StarRatingComponent {
    @Input() stars: number = 0;

    filledStars(): Array<number> {
        return Array(this.stars);
    }

    emptyStars(): Array<number> {
        return Array(5 - this.stars);
    }
}

/**
 * Une classe qui effectue des actions sur les articles.
 */
export abstract class ArticleUser {
    filledStars(n: number): Array<number> {
        return Array(n);
    }

    emptyStars(n: number): Array<number> {
        return Array(5 - n);
    }

    getImagePath(image: string | null, name: string): string {
        if (image == null) {
            let code =
                new Array(name)
                    .map((c) => c.charCodeAt(0))
                    .reduce((a, b) => a + b) % 4;
            return "/assets/default-" + code + ".png";
        }

        return "todo";
    }
}

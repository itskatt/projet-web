/**
 * Une classe qui effectue des actions sur les articles.
 */
export abstract class ArticleUser {
    getImagePath(image: string | null, name: string): string {
        if (image == null) {
            let code =
                new Array(name)
                    .map((c) => c.charCodeAt(0))
                    .reduce((a, b) => a + b) % 4;
            return "/assets/default-" + code + ".png";
        }

        return "/image.php?id=" + image;
    }
}

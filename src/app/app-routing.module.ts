import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ArticlesGridComponent } from './articles-grid/articles-grid.component';

const routes: Routes = [
  {
    path: "",
    component: ArticlesGridComponent
  }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }

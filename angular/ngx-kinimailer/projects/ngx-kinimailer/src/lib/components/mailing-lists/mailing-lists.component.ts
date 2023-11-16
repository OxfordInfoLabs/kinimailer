import {Component, OnInit} from '@angular/core';
import {MailingListService} from '../../services/mailing-list.service';
import {debounceTime, map, switchMap} from 'rxjs/operators';
import {BehaviorSubject, merge, Subject} from 'rxjs';

@Component({
    selector: 'km-mailing-lists',
    templateUrl: './mailing-lists.component.html',
    styleUrls: ['./mailing-lists.component.sass']
})
export class MailingListsComponent implements OnInit {

    public mailingLists: any = [];
    public searchText = new BehaviorSubject('');
    public limit = 1000;
    public offset = 0;
    public page = 1;
    public endOfResults = false;
    public loading = true;

    private reload = new Subject();

    constructor(private mailingListService: MailingListService) {
    }

    ngOnInit(): void {
        merge(this.searchText, this.reload)
            .pipe(
                debounceTime(300),
                // distinctUntilChanged(),
                switchMap(() =>
                    this.getMailingLists()
                )
            ).subscribe((mailingLists: any) => {
            this.endOfResults = mailingLists.length < this.limit;
            this.mailingLists = mailingLists;
            this.loading = false;
        });

        this.searchText.subscribe(() => {
            this.page = 1;
            this.offset = 0;
        });
    }

    private getMailingLists() {
        return this.mailingListService.filterMailingList(
            this.searchText.getValue() || '',
            this.offset.toString(),
            this.limit.toString()
        ).pipe(map((feeds: any) => {
                return feeds;
            })
        );
    }
}

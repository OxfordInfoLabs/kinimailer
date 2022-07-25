import {Component, OnInit} from '@angular/core';
import {BehaviorSubject, merge, Subject} from 'rxjs';
import {MailingService} from '../../services/mailing.service';
import {debounceTime, map, switchMap} from 'rxjs/operators';

@Component({
    selector: 'km-mailings',
    templateUrl: './mailings.component.html',
    styleUrls: ['./mailings.component.sass']
})
export class MailingsComponent implements OnInit {

    public mailings: any = [];
    public searchText = new BehaviorSubject('');
    public limit = 10;
    public offset = 0;
    public page = 1;
    public endOfResults = false;
    public loading = true;

    private reload = new Subject();

    constructor(private mailingService: MailingService) {
    }

    ngOnInit(): void {
        merge(this.searchText, this.reload)
            .pipe(
                debounceTime(300),
                // distinctUntilChanged(),
                switchMap(() =>
                    this.getMailings()
                )
            ).subscribe((mailings: any) => {
            this.endOfResults = mailings.length < this.limit;
            this.mailings = mailings;
            this.loading = false;
        });

        this.searchText.subscribe(() => {
            this.page = 1;
            this.offset = 0;
        });
    }

    private getMailings() {
        return this.mailingService.filterMailings(
            this.searchText.getValue() || '',
            this.offset.toString(),
            this.limit.toString()
        ).pipe(map((feeds: any) => {
                return feeds;
            })
        );
    }
}

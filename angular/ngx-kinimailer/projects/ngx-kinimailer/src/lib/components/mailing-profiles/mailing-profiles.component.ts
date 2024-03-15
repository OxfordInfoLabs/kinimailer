import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';
import {BehaviorSubject, merge, Subject} from 'rxjs';
import {debounceTime, map, switchMap} from 'rxjs/operators';

@Component({
    selector: 'km-mailing-profiles',
    templateUrl: './mailing-profiles.component.html',
    styleUrls: ['./mailing-profiles.component.sass']
})
export class MailingProfilesComponent implements OnInit {

    @Input() selected: any = {};
    @Input() hideFooter = false;
    @Input() allowSelection = false;
    @Input() mailingProfileService: any;

    @Output() selectedChange = new EventEmitter();

    public mailingProfiles: any = [];
    public mailingProfileSearchText = new BehaviorSubject('');
    public limit = 1000;
    public offset = 0;
    public page = 1;
    public endOfResults = false;
    public loading = true;
    public newMailingProfile: any = {};
    public addMailingProfile = false;

    private reload = new Subject();

    constructor() {
    }

    ngOnInit(): void {
        merge(this.mailingProfileSearchText, this.reload)
            .pipe(
                debounceTime(300),
                // distinctUntilChanged(),
                switchMap(() =>
                    this.getMailingLists()
                )
            ).subscribe((mailingProfiles: any) => {
            this.endOfResults = mailingProfiles.length < this.limit;
            this.mailingProfiles = mailingProfiles;
            this.loading = false;
        });
    }

    public search(event: any) {
        this.mailingProfileSearchText.next(event.target.value);
    }

    public makeSelection(mailingProfile) {
        if (this.allowSelection) {
            this.selected = mailingProfile;
            this.selectedChange.emit(mailingProfile);
        }
    }

    public cancelNewMailingProfile() {
        this.newMailingProfile = {};
        this.addMailingProfile = false;
    }

    public async saveNewMailingProfile() {
        await this.mailingProfileService.saveMailingProfile(this.newMailingProfile);
        this.newMailingProfile = {};
        this.addMailingProfile = false;
        this.reload.next(Date.now());
    }

    public async removeMailingProfile(id) {
        const message = 'Are you sure you would like to remove this Mailing Profile?';
        if (window.confirm(message)) {
            await this.mailingProfileService.removeMailingProfile(id);
            this.reload.next(Date.now());
            if (this.selected && this.selected.id === id) {
                this.selectedChange.next(null);
                this.selected = null;
            }
        }
    }

    private getMailingLists() {
        return this.mailingProfileService.getMailingProfiles(
            this.mailingProfileSearchText.getValue() || '',
            this.offset.toString(),
            this.limit.toString()
        ).pipe(map((mailingProfiles: any) => {
                return mailingProfiles || [];
            })
        );
    }

}

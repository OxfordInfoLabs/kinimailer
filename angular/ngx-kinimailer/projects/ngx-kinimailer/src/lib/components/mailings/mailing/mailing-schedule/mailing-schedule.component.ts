import {Component, OnInit} from '@angular/core';
import {ActivatedRoute} from '@angular/router';
import {MailingService} from '../../../../services/mailing.service';
import * as _ from 'lodash';

@Component({
    selector: 'km-mailing-schedule',
    templateUrl: './mailing-schedule.component.html',
    styleUrls: ['./mailing-schedule.component.sass']
})
export class MailingScheduleComponent implements OnInit {

    public mailing: any;
    public mailingEmailAddresses: string = '';
    public Object = Object;
    public _ = _;
    public showNewTaskTimePeriod = false;
    public newTaskTimePeriod: any = {};
    public months = _.range(1, 29);
    public hours = _.range(0, 24);
    public minutes = _.range(0, 60);

    private mailingId: number;

    constructor(private route: ActivatedRoute,
                private mailingService: MailingService) {
    }

    ngOnInit(): void {
        this.route.params.subscribe(params => {
            this.mailingId = params.id;
            this.loadMailing();
        });
    }

    public convertKey(key) {
        return _.startCase(key);
    }

    public addScheduleTime() {
        this.showNewTaskTimePeriod = true;
    }

    public addTimePeriod() {
        this.mailing.scheduledTask.timePeriods.push(this.newTaskTimePeriod);
        this.showNewTaskTimePeriod = false;
        this.newTaskTimePeriod = {};
    }

    public removeTime(index) {
        this.mailing.scheduledTask.timePeriods.splice(index, 1);
        if (!this.mailing.scheduledTask.timePeriods.length) {
            this.showNewTaskTimePeriod = true;
            this.newTaskTimePeriod = {};
        }
    }

    public finalise() {

    }

    public async save() {
        if (this.mailingEmailAddresses) {
            this.mailing.emailAddresses = this.mailingEmailAddresses.split(',');
        }
        await this.mailingService.saveMailing(this.mailing);
    }

    private async loadMailing(): Promise<void> {
        this.mailing = await this.mailingService.getMailing(this.mailingId);
        if (this.mailing.emailAddresses && this.mailing.emailAddresses.length) {
            this.mailingEmailAddresses = this.mailing.emailAddresses.join(',');
        }
        if (!this.mailing.scheduledTask) {
            this.mailing.scheduledTask = {
                taskIdentifier: _.kebabCase(this.mailing.title) + '-mailing-schedule-' + Date.now(),
                description: 'Mailing Schedule for ' + this.mailing.title,
                configuration: {mailingId: this.mailing.id},
                timePeriods: []
            };
        }
    }
}